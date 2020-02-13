<?php

    class Instructions
    {
        const INSTRUCTIONS = array(
            "write" => ["string"]
        );

    }

    class Errors
    {

    }

    class Types
    {

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
        public function checkNumOfParameters($opCode, $numberOfParameters) {
            return count(Instructions::INSTRUCTIONS[$opCode]) == $numberOfParameters;
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

    while(!feof($fileManager->getFile())){
        print_r($lexicalChecker->getNextToken());
    }

