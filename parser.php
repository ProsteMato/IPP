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
            "POPS" => [Types::VAR],
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
        const COMMENT = "(#[^\n]*)";
        const DELIMITER = "[\s\t]";
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
        private $lexicalChecker;
        private $syntaxChecker;
        private $instruction;
        private $xmlGenerator;
        private $fileManager;

        public function __construct($lexicalChecker, $syntaxChecker, $xmlGenerator, $fileManager)
        {
            $this->lexicalChecker = $lexicalChecker;
            $this->syntaxChecker = $syntaxChecker;
            $this->xmlGenerator = $xmlGenerator;
            $this->fileManager = $fileManager;
        }

        public function parse()
        {
            $token = $this->lexicalChecker->getNextToken();
            if(!$this->syntaxChecker->checkFirstToken($token)) {
                throw new Exception("Bad Header", Errors::HEADER_ERR);
            }
            while(!feof($this->fileManager->getFile()))
            {
                $token = $this->lexicalChecker->getNextToken();
            }
        }
    }

    class Instruction
    {
        private $opCode;
        private $order;
        private $arguments;

        public function __construct()
        {
        }

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
        private $type;
        private $content;

        public function __construct()
        {
        }

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

    interface IGenerator
    {
        public function generate();
    }

    class XmlGenerator implements IGenerator
    {
        public function generate()
        {
            // TODO: Implement generate() method.
        }
    }

    class SyntaxChecker
    {
        public function checkNumOfParameters($opCode, $numberOfParameters)
        {
            return count(Instructions::INSTRUCTIONS[$opCode]) == $numberOfParameters;
        }

        public function checkFirstToken($token)
        {
            $token = mb_strtolower($token[0]);
            return strcmp(".ippcode20", $token) == 0;
        }
    }

    class LexicalChecker
    {
        private $fileManager;

        public function __construct($fileManager)
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

        public function checkOpCode($opCode) {
            return array_key_exists($opCode, Instructions::INSTRUCTIONS);
        }
    }

    class Stats
    {
        private $loc;
        private $comments;
        private $labels;
        private $jumps;
        private $file;
    }

    $fileManager = new FileManager(STDIN);
    $lexicalChecker = new LexicalChecker($fileManager);
    $syntaxChecker = new SyntaxChecker();
    $xmlGenerator = new XmlGenerator();
    $parser = new Parser($lexicalChecker, $syntaxChecker, $xmlGenerator, $fileManager);
    try {
        $parser->parse();
    } catch (Exception $e) {
        exit($e->getCode());
    }




