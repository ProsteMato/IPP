<?php
/**
 * @class   Analysis
 * @file    Analysis.php
 * @date    1.3.2020
 * @author  Martin Koči (xkocim05@stud.fit.vutbr.cz)
 * @brief   This class will analyze lexical and syntax correction of src file in IPPcode20
 */

class Analysis
{
    private Stats $stats;

    /**
     * Analysis constructor.
     * @param Stats $stats
     */
    public function __construct(Stats $stats)
    {
        $this->stats = $stats;
    }

    /**
     * @param array $token    line from input src file.
     * @return array     Arguments from instruction
     * @throws InvalidInstructionException
     */
    public function argParser($token) : array
    {
        $this->isOpCode($token[0]);
        $this->checkNumOfParameters($token[0], count(array_slice($token, 1)));
        return $this->checkArguments(array_slice($token, 1), Instructions::INSTRUCTIONS[strtoupper($token[0])]);
    }

    /**
     * @param array $token input token from src file.
     * @return bool if ending token
     */
    public function isEndingToken($token)
    {
        return $token == null;
    }

    /**
     * @param array $token Input token from src file
     * @throws InvalidHeaderException
     */
    public function isHeader($token)
    {
        $token = mb_strtolower($token[0]);
        if(strcmp(".ippcode20", $token) != 0)
            throw new InvalidHeaderException(basename(__FILE__)."::".__FUNCTION__." - \"$token\" is not correct header of IPPCode20 language!", Errors::HEADER_ERR);
    }

    private function checkNumOfParameters($opCode, $numberOfParameters)
    {
        if(count(Instructions::INSTRUCTIONS[mb_strtoupper($opCode)]) != $numberOfParameters)
            throw new InvalidArgumentException(basename(__FILE__)."::".__FUNCTION__." - Bad argument count in instruction \"$opCode\"!", Errors::LEX_OR_SYNTAX_ERR);
    }

    private function isOpCode($opCode)
    {
        $opCode = mb_strtoupper($opCode);
        if(!array_key_exists($opCode, Instructions::INSTRUCTIONS))
            throw new InvalidInstructionException(basename(__FILE__)."::".__FUNCTION__." - OpCode \"$opCode\" is undefined!", Errors::INSTRUCTION_ERR);
        $this->statsJumpsIncrementation($opCode);
    }

    private function statsJumpsIncrementation($opCode)
    {
        if (strcmp($opCode, "JUMP") == 0 ||
            strcmp($opCode, "JUMPIFEQ") == 0 ||
            strcmp($opCode, "JUMPIFNEQ") == 0 ||
            strcmp($opCode, "CALL") == 0 ||
            strcmp($opCode, "RETURN") == 0) {
            $this->stats->incJumps();
        }
    }

    private function isVariable($variable)
    {
        return preg_match("/^" . Regex::VARIABLE . "/", $variable);
    }

    private function isSymbol($symbol)
    {
        return preg_match("/^" . Regex::SYMBOL . "/", $symbol);
    }

    private function isLabel($label)
    {
        return preg_match("/^" . Regex::LABEL . "/", $label);
    }

    private function isType($type)
    {
        return preg_match("/^" . Regex::TYPE . "/", $type);
    }

    private function checkArguments($givenArguments, $requiredArguments)
    {
        $arguments = array();
        for ($index = 0; $index < count($requiredArguments); $index++)
        {
            $this->checkArgumentTypes($requiredArguments[$index], $givenArguments[$index]);
            switch ($requiredArguments[$index])
            {
                case Types::SYMBOL:
                    if($this->isVariable($givenArguments[$index])) {
                        array_push($arguments, new Arguments("var", $givenArguments[$index]));
                    } else {
                        $splitArgument = preg_split("/[@]/", $givenArguments[$index], 2);
                        array_push($arguments, new Arguments($splitArgument[0], $splitArgument[1]));
                    }
                    break;
                case Types::TYPE:
                case Types::VARIABLE:
                case Types::LABEL:
                    array_push($arguments, new Arguments($requiredArguments[$index], $givenArguments[$index]));
                    break;
            }
        }
        return $arguments;
    }

    private function checkArgumentTypes($type, $content){
        switch ($type)
        {
            case Types::SYMBOL:
                if (!$this->isSymbol($content))
                    throw new InvalidArgumentException(basename(__FILE__)."::".__FUNCTION__." - Symbol \"$content\" is not valid!");
                break;
            case Types::TYPE:
                if (!$this->isType($content))
                    throw new InvalidArgumentException(basename(__FILE__)."::".__FUNCTION__." - Type \"$content\" is not valid!");
                break;
            case Types::VARIABLE:
                if (!$this->isVariable($content))
                    throw new InvalidArgumentException(basename(__FILE__)."::".__FUNCTION__." - Variable \"$content\" is not valid!");
                break;
            case Types::LABEL:
                if (!$this->isLabel($content))
                    throw new InvalidArgumentException(basename(__FILE__)."::".__FUNCTION__."- Label \"$content\" is not valid!");
        }
    }
}
