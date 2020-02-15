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
        private LexicalChecker $lexicalChecker;
        private SyntaxChecker $syntaxChecker;
        private Instruction $instruction;
        private XmlGenerator $xmlGenerator;
        private FileManager $fileManager;
        private int $order;

        public function __construct(LexicalChecker $lexicalChecker, SyntaxChecker $syntaxChecker, XmlGenerator $xmlGenerator, FileManager $fileManager, Instruction $instruction)
        {
            $this->lexicalChecker = $lexicalChecker;
            $this->syntaxChecker = $syntaxChecker;
            $this->xmlGenerator = $xmlGenerator;
            $this->fileManager = $fileManager;
            $this->instruction = $instruction;
            $this->order = 1;
        }

        public function parse()
        {
            $token = $this->lexicalChecker->getNextToken();
            try {
                $this->syntaxChecker->checkFirstToken($token);
                while (!feof($this->fileManager->getFile())) {
                    $token = $this->lexicalChecker->getNextToken();
                    if (feof($this->fileManager->getFile())) {
                        break;
                    }
                    $this->lexicalChecker->checkOpCode($token[0]);
                    $this->syntaxChecker->checkNumOfParameters($token[0], count(array_slice($token, 1)));

                    $this->argParse(array_slice($token, 1), Instructions::INSTRUCTIONS[$token[0]]);
                }
            } catch (Exception $e) {
                throw $e;
            }
        }

        private function argParse($arguments, $requiredArguments)
        {
            print_r($arguments);
            for ($i = 0; $i < count($requiredArguments); $i++)
            {
                try {
                    switch ($requiredArguments[$i])
                    {
                        case Types::VARIABLE:
                            $this->lexicalChecker->isVariable($arguments[$i]);
                            break;
                        case Types::LABEL:
                            $this->lexicalChecker->isLabel($arguments[$i]);
                            break;
                        case Types::SYMBOL:
                            $this->lexicalChecker->isSymbol($arguments[$i]);
                            break;
                        case Types::TYPE:
                            $this->lexicalChecker->isType($arguments[$i]);
                            break;
                    }
                } catch (Exception $e) {
                    throw $e;
                }
            }
        }
    }

    class Instruction
    {
        private string $opCode;
        private string $order;
        private string $arguments;

        public function getOrder()
        {
            return $this->order;
        }

        public function getOpCode()
        {
            return $this->opCode;
        }

        public function getArguments()
        {
            return $this->arguments;
        }
    }

    class Arguments
    {
        private string $type;
        private string $content;

        public function getType()
        {
            return $this->type;
        }

        public function setType($type)
        {
            $this->type = $type;
        }

        public function getContent()
        {
            return $this->content;
        }

        public function setContent($content)
        {
            $this->content = $content;
        }
    }

    class XmlGenerator
    {
        public function generateInstruction()
        {

        }

        public function generateHeader()
        {

        }
    }

    class SyntaxChecker
    {
        public function checkNumOfParameters($opCode, $numberOfParameters)
        {
            if(count(Instructions::INSTRUCTIONS[$opCode]) != $numberOfParameters)
                throw new Exception("Bad argument count in instruction!", Errors::BAD_ARGUMENT);
        }

        public function checkFirstToken($token)
        {
            $token = mb_strtolower($token[0]);
            if(!strcmp(".ippcode20", $token) == 0)
                throw new Exception("Bad header content!", Errors::HEADER_ERR);
        }
    }

    class LexicalChecker
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
        }

        public function checkOpCode($opCode)
        {
            $opCode = mb_strtoupper($opCode);
            if(!array_key_exists($opCode, Instructions::INSTRUCTIONS))
                throw new Exception("Undefined opCode", Errors::INSTRUCTION_ERR);
        }

        public function isVariable($variable)
        {
            if(!preg_match("/^" . Regex::VARIABLE . "/", $variable))
                throw new Exception("Variable argument is not valid!", Errors::LEX_OR_SYNTAX_ERR);
        }

        public function isSymbol($symbol)
        {
            if(!preg_match("/^" . Regex::SYMBOL . "/", $symbol))
                throw new Exception("Symbol argument is not valid!", Errors::LEX_OR_SYNTAX_ERR);
        }

        public function isLabel($label)
        {
            if(!preg_match("/^" . Regex::LABEL . "/", $label))
                throw new Exception("Label argument is not valid!", Errors::LEX_OR_SYNTAX_ERR);
        }

        public function isType($type)
        {
            if(!preg_match("/^" . Regex::TYPE . "/", $type))
                throw new Exception("Type argument is not valid!", Errors::LEX_OR_SYNTAX_ERR);
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
    $lexicalChecker = new LexicalChecker($fileManager);
    $syntaxChecker = new SyntaxChecker();
    $xmlGenerator = new XmlGenerator();
    $instruction = new Instruction();
    $parser = new Parser($lexicalChecker, $syntaxChecker, $xmlGenerator, $fileManager, $instruction);
    try {
        $parser->parse();
    } catch (Exception $e) {
        exit($e->getCode());
    }




