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

        private function getLine()
        {
            return fgets($this->file);
        }

        public function getNextToken()
        {
            while(!feof($this->file)) {
                $line = $this->getLine();
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
    }

    class Parser
    {
        private Analysis $analysis;
        private Instruction $instruction;
        private FileManager $fileManager;
        private XmlGenerator $xmlGenerator;
        private int $order;

        public function __construct(Analysis $analysis, XmlGenerator $xmlGenerator, FileManager $fileManager, Instruction $instruction)
        {
            $this->analysis = $analysis;
            $this->xmlGenerator = $xmlGenerator;
            $this->fileManager = $fileManager;
            $this->instruction = $instruction;
            $this->order = 1;
        }

        public function parse()
        {
            $token = $this->fileManager->getNextToken();
            $this->analysis->isHeader($token);
            $this->xmlGenerator->generateHeader();
            $token = $this->fileManager->getNextToken();
            while (!$this->analysis->isEndingToken($token)) {
                $arguments = $this->analysis->argParser($token);
                $this->instruction->setOpCode($token[0]);
                $this->instruction->setArguments($arguments);
                $this->xmlGenerator->generateInstruction($this->instruction, $this->order);
                $token = $this->fileManager->getNextToken();
                $this->order++;
            }
            $this->xmlGenerator->generate();
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

        public function setArguments($arguments) {
            $this->arguments = $arguments;
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
        private XMLWriter $xmlWriter;

        public function __construct()
        {
            $this->xmlWriter = new XMLWriter();
        }

        public function generateInstruction(Instruction $instruction, int $order)
        {
            $arguments = $instruction->getArguments();
            $this->xmlWriter->startElement("instruction");
            $this->xmlWriter->writeAttribute("order", $order);
            $this->xmlWriter->writeAttribute("opcode", $instruction->getOpCode());
            for($i = 1; $i <= count($arguments); $i++) {
                $this->xmlWriter->startElement("arg" . $i);
                $this->xmlWriter->writeAttribute("type", $arguments[$i - 1]->getType());
                $content = htmlspecialchars($arguments[$i - 1]->getContent(), ENT_XML1 | ENT_QUOTES, "UTF-8");
                $this->xmlWriter->writeRaw($content);
                $this->xmlWriter->endElement();
            }
            $this->xmlWriter->endElement();
        }

        public function generate(){
            $this->xmlWriter->endElement();
            $this->xmlWriter->endDocument();
            echo $this->xmlWriter->outputMemory(true);
        }

        public function generateHeader()
        {
            $this->xmlWriter->openMemory();
            $this->xmlWriter->setIndent(true);
            $this->xmlWriter->setIndentString("    ");
            $this->xmlWriter->startDocument("1.0", "UTF-8");
            $this->xmlWriter->startElement("program");
            $this->xmlWriter->writeAttribute("language", "IPPcode20");
        }
    }

    class Analysis
    {
        private function checkNumOfParameters($opCode, $numberOfParameters)
        {
            if(count(Instructions::INSTRUCTIONS[$opCode]) != $numberOfParameters)
                throw new Exception("Bad argument count in instruction!", Errors::BAD_ARGUMENT);
        }

        public function isHeader($token)
        {
            $token = mb_strtolower($token[0]);
            if(strcmp(".ippcode20", $token) != 0)
                throw new Exception("Bad header content!", Errors::HEADER_ERR);
        }

        private function isOpCode($opCode)
        {
            $opCode = mb_strtoupper($opCode);
            if(!array_key_exists($opCode, Instructions::INSTRUCTIONS))
                throw new Exception("Undefined opCode!", Errors::INSTRUCTION_ERR);
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

        public function isEndingToken($token)
        {
            return $token == null;
        }

        public function argParser($token) : array
        {
            $this->isOpCode($token[0]);
            $this->checkNumOfParameters($token[0], count(array_slice($token, 1)));
            return $this->checkArguments(array_slice($token, 1), Instructions::INSTRUCTIONS[$token[0]]);
        }

        private function checkArguments($givenArguments, $requiredArguments)
        {
            $arguments = array();
            for ($index = 0; $index < count($requiredArguments); $index++)
            {
                switch ($requiredArguments[$index])
                {
                    case Types::VARIABLE:
                        if (!$this->isVariable($givenArguments[$index]))
                            throw new Exception("Variable is not valid!", Errors::LEX_OR_SYNTAX_ERR);
                        array_push($arguments, new Arguments("var", $givenArguments[$index]));
                        break;
                    case Types::LABEL:
                        if (!$this->isLabel($givenArguments[$index]))
                            throw new Exception("Label is not valid!", Errors::LEX_OR_SYNTAX_ERR);
                        array_push($arguments, new Arguments("label", $givenArguments[$index]));
                        break;
                    case Types::SYMBOL:
                        if (!$this->isSymbol($givenArguments[$index]))
                            throw new Exception("Symbol is not valid!", Errors::LEX_OR_SYNTAX_ERR);
                        if($this->isVariable($givenArguments[$index])) {
                            array_push($arguments, new Arguments("var", $givenArguments[$index]));
                        } else {
                            $splitArgument = preg_split("/[@]/", $givenArguments[$index], 2);
                            array_push($arguments, new Arguments($splitArgument[0], $splitArgument[1]));
                        }
                        break;
                    case Types::TYPE:
                        if (!$this->isType($givenArguments[$index]))
                            throw new Exception("Type is not valid!", Errors::LEX_OR_SYNTAX_ERR);
                        array_push($arguments, new Arguments("type", $givenArguments[$index]));
                        break;
                }
            }
            return $arguments;
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
    $analysis = new Analysis();
    $xmlGenerator = new XmlGenerator();
    $instruction = new Instruction();
    $parser = new Parser($analysis, $xmlGenerator, $fileManager, $instruction);
    try {
        $parser->parse();
    } catch (Exception $e) {
        exit($e->getCode());
    }

