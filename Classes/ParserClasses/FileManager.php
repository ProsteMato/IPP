<?php


class FileManager
{
    private $file;
    private Stats $stats;

    public function __construct($file, Stats $stats)
    {
        $this->file = $file;
        $this->stats = $stats;
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
                $this->stats->incComments();
            }

            if(count($token) > 0) {
                return $token;
            }
        }
        return null;
    }
}
