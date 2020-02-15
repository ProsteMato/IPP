<?php

    class Instructions
    {
        const INSTRUCTIONS = array(
            "MOVE" => [Types::VARIABLE, Types::SYMBOL],
            "CREATEFRAME" => [],
            "PUSHFRAME" => [],
            "POPFRAME" => [],
            "DEFVAR" => [Types::VARIABLE],
            "CALL" => [Types::LABEL],
            "RETURN" => [],
            "PUSHS" => [Types::SYMBOL],
            "POPS" => [Types::VARIABLE],
            "ADD" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
            "SUB" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
            "MUL" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
            "IDIV" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
            "LT" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
            "GT" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
            "EQ" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
            "AND" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
            "OR" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
            "NOT" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
            "INT2CHAR" => [Types::VARIABLE, Types::SYMBOL],
            "STR2INT" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
            "READ" => [Types::VARIABLE, Types::TYPE],
            "WRITE" => [Types::SYMBOL],
            "CONCAT" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
            "STRLEN" => [Types::VARIABLE, Types::SYMBOL],
            "GETCHAR" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
            "SETCHAR" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
            "TYPE" => [Types::VARIABLE, Types::SYMBOL],
            "LABEL" => [Types::LABEL],
            "JUMP" => [Types::LABEL],
            "JUMPIFEQ" => [Types::LABEL, Types::SYMBOL, Types::SYMBOL],
            "JUMPIFNEQ" => [Types::LABEL, Types::SYMBOL, Types::SYMBOL],
            "EXIT" => [Types::SYMBOL],
            "DPRINT" => [Types::SYMBOL],
            "BREAK" => []
        );

    }

    class Errors
    {
        const BAD_ARGUMENT = 10;
        const HEADER_ERR = 21;
        const INSTRUCTION_ERR = 22;
        const LEX_OR_SYNTAX_ERR = 23;
    }

    class Types
    {
        const VARIABLE = 1;
        const LABEL = 2;
        const SYMBOL = 3;
        const TYPE = 4;
    }

    class Regex
    {
        private const STRING = "string@(([^\\\#]|\\\\\d{3})+|$)";
        private const INT = "int@(\+|\-)?\d+";
        private const BOOL = "bool@(true|false)";
        private const NIL = "nil@nil";
        private const CONST = self::STRING . "|" . self::INT . "|" . self::BOOL . "|" . self::NIL;
        private const SPECIAL_CHAR = "_$&%*!?-";
        private const IDENTIFIER = "[[:alpha:]" . self::SPECIAL_CHAR . "][[:alnum:]" . self::SPECIAL_CHAR . "]*";
        const COMMENT = "(#[^\n]*)";
        const DELIMITER = "[\s\t]";
        const TYPE = "(int|bool|string)";
        const VARIABLE = "(GF|LF|TF)@" . self::IDENTIFIER;
        const LABEL = self::IDENTIFIER;
        const SYMBOL = "(". self::CONST . "|" . self::VARIABLE . ")";
    }

    class FileManager
    {
        private $file;

        public function __construct($file)
        {
            $this->file = $file;
        }

        public function getFile()
        {
            return $this->file;
        }

        public function getLine()
        {
            return fgets($this->file);
        }
    }

    class Parser
    {
        private Analysis $analysis;
        private Instruction $instruction;
        private XmlGenerator $xmlGenerator;
        private int $order;

        public function __construct(Analysis $analysis, XmlGenerator $xmlGenerator, Instruction $instruction)
        {
            $this->analysis = $analysis;
            $this->xmlGenerator = $xmlGenerator;
            $this->instruction = $instruction;
            $this->order = 1;
        }

        public function parse()
        {
            $token = $this->analysis->getNextToken();
            $this->analysis->isHeader($token);
            $token = $this->analysis->getNextToken();
            while (!$this->analysis->isEndingToken($token)) {
                $this->analysis->isOpCode($token[0]);
                $this->analysis->checkNumOfParameters($token[0], count(array_slice($token, 1)));

                $this->argParse(array_slice($token, 1), Instructions::INSTRUCTIONS[$token[0]]);
                $this->instruction->setOpCode($token[0]);
                $token = $this->analysis->getNextToken();
            }
        }

        private function argParse($givenArguments, $requiredArguments)
        {
            for ($index = 0; $index < count($requiredArguments); $index++)
            {
                switch ($requiredArguments[$index])
                {
                    case Types::VARIABLE:
                        if (!$this->analysis->isVariable($givenArguments[$index]))
                            throw new Exception("Variable is not valid!", Errors::LEX_OR_SYNTAX_ERR);
                        $this->instruction->setArguments($requiredArguments[$index], $givenArguments[$index], $this->analysis);
                        break;
                    case Types::LABEL:
                        if (!$this->analysis->isLabel($givenArguments[$index]))
                            throw new Exception("Label is not valid!", Errors::LEX_OR_SYNTAX_ERR);
                        $this->instruction->setArguments($requiredArguments[$index], $givenArguments[$index], $this->analysis);
                        break;
                    case Types::SYMBOL:
                        if (!$this->analysis->isSymbol($givenArguments[$index]))
                            throw new Exception("Symbol is not valid!", Errors::LEX_OR_SYNTAX_ERR);
                        $this->instruction->setArguments($requiredArguments[$index], $givenArguments[$index], $this->analysis);
                        break;
                    case Types::TYPE:
                        if (!$this->analysis->isType($givenArguments[$index]))
                            throw new Exception("Type is not valid!", Errors::LEX_OR_SYNTAX_ERR);
                        $this->instruction->setArguments($requiredArguments[$index], $givenArguments[$index], $this->analysis);
                        break;
                }
            }
        }
    }

    class Instruction
    {
        private string $opCode;
        private array $arguments;

        public function __construct()
        {
            $this->opCode = "";
            $this->arguments = array();
        }

        public function getOpCode()
        {
            return $this->opCode;
        }

        public function getArguments()
        {
            return $this->arguments;
        }

        public function setOpCode(string $opCode)
        {
            $this->opCode = $opCode;
        }

        public function setArguments($type, string $givenArgument, Analysis $analysis) {
            switch($type)
            {
                case Types::VARIABLE:
                    array_push($this->arguments, new Arguments("var", $givenArgument));
                    break;
                case Types::LABEL:
                    array_push($this->arguments, new Arguments("label", $givenArgument));
                    break;
                case Types::SYMBOL:
                    if($analysis->isVariable($givenArgument)) {
                        array_push($this->arguments, new Arguments("var", $givenArgument));
                    } else {
                        $splitArgument = preg_split("/[@]/", $givenArgument, 2);
                        array_push($this->arguments, new Arguments($splitArgument[0], $splitArgument[1]));
                    }
                    break;
                case Types::TYPE:
                    array_push($this->arguments, new Arguments("type", $givenArgument));
                    break;
            }
        }
    }

    class Arguments
    {
        private string $type;
        private string $content;

        public function __construct(string $type, string $content)
        {
            $this->type = $type;
            $this->content = $content;
        }

        public function getType()
        {
            return $this->type;
        }

        public function getContent()
        {
            return $this->content;
        }
    }

    class XmlGenerator
    {
        public function generateInstruction($instruction)
        {

        }

        public function generateHeader()
        {

        }
    }

    class Analysis
    {
        private FileManager $fileManager;

        public function __construct(FileManager $fileManager)
        {
            $this->fileManager = $fileManager;
        }

        public function getNextToken()
        {
            while(!feof($this->fileManager->getFile())) {
                $line = $this->fileManager->getLine();
                $token = preg_split("/". Regex::DELIMITER . "|" . Regex::COMMENT . "/", $line, 0,
                    PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

                if(count($token) > 0 && preg_match("/^". Regex::COMMENT ."/",$token[array_key_last($token)])) {
                    unset($token[array_key_last($token)]);
                }

                if(count($token) > 0) {
                    return $token;
                }
            }
            return null;
        }

        public function checkNumOfParameters($opCode, $numberOfParameters)
        {
            if(count(Instructions::INSTRUCTIONS[$opCode]) != $numberOfParameters)
                throw new Exception("Bad argument count in instruction!", Errors::BAD_ARGUMENT);
        }

        public function isHeader($token)
        {
            $token = mb_strtolower($token[0]);
            if(!strcmp(".ippcode20", $token) == 0)
                throw new Exception("Bad header content!", Errors::HEADER_ERR);
        }

        public function isOpCode($opCode)
        {
            $opCode = mb_strtoupper($opCode);
            if(!array_key_exists($opCode, Instructions::INSTRUCTIONS))
                throw new Exception("Undefined opCode!", Errors::INSTRUCTION_ERR);
        }

        public function isVariable($variable)
        {
            return preg_match("/^" . Regex::VARIABLE . "/", $variable);
        }

        public function isSymbol($symbol)
        {
            return preg_match("/^" . Regex::SYMBOL . "/", $symbol);
        }

        public function isLabel($label)
        {
            return preg_match("/^" . Regex::LABEL . "/", $label);
        }

        public function isType($type)
        {
            return preg_match("/^" . Regex::TYPE . "/", $type);
        }

        public function isEndingToken($token)
        {
            return $token == null;
        }
    }

    class Stats
    {
        private int $loc;
        private int $comments;
        private int $labels;
        private int $jumps;
        private int $file;

        public function setFile($file) {
            $this->file = $file;
        }

        public function incLoc() {
            $this->loc++;
        }

        public function incComments() {
            $this->comments++;
        }

        public function incLabels() {
            $this->labels++;
        }

        public function incJumps() {
            $this->jumps++;
        }

        public function getLoc(): int
        {
            return $this->loc;
        }

        public function getComments(): int
        {
            return $this->comments;
        }

        public function getLabels(): int
        {
            return $this->labels;
        }

        public function getJumps(): int
        {
            return $this->jumps;
        }

        public function getFile(): int
        {
            return $this->file;
        }
    }

    $fileManager = new FileManager(STDIN);
    $analysis = new Analysis($fileManager);
    $xmlGenerator = new XmlGenerator();
    $instruction = new Instruction();
    $parser = new Parser($analysis, $xmlGenerator, $instruction);
    try {
        $parser->parse();
    } catch (Exception $e) {
        exit($e->getCode());
    }




