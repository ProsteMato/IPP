<?php
/**
 * @file    FileManager.php
 * @class   FileManager
 * @date    1.3.2020
 * @author  Martin Koči (xkocim05@stud.fit.vutbr.cz)
 * @brief   This class is for managing input file and getting tokens
 */

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

    /**
     * @brief This method is reading line by line from input file and parsing the input line and returning it.
     * @return array|null
     */
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
