<?php
/**
 * @file    Stats.php
 * @class   Stats
 * @date    1.3.2020
 * @author  Martin Koči (xkocim05@stud.fit.vutbr.cz)
 * @brief   This class is for storing the analysis of src file and output it to the output file if necessary.
 */

class Stats
{
    private int $loc;
    private int $comments;
    private int $labels;
    private int $jumps;

    /**
     * Stats constructor.
     */
    public function __construct()
    {
        $this->loc = 0;
        $this->jumps = 0;
        $this->labels = 0;
        $this->comments = 0;
    }

    /**
     * @brief increment the loc variable
     */
    public function incLoc() {
        $this->loc++;
    }

    /**
     * @brief increment the comments variable
     */
    public function incComments() {
        $this->comments++;
    }

    /**
     * @brief increment the labels variable
     */
    public function incLabels() {
        $this->labels++;
    }

    /**
     * @brief increment the jumps variable
     */
    public function incJumps() {
        $this->jumps++;
    }

    /**
     * @param $parsedArguments  array information where and what to generate
     * @throws PermissionException
     * @brief   This function generates the statistics about src input file.
     */
    public function generateStats($parsedArguments)
    {
        if (is_file($parsedArguments["stats"]) && !is_writable($parsedArguments["stats"])) {
            throw new PermissionException(basename(__FILE__)."::".__FUNCTION__."File \"{$parsedArguments["stats"]}\" is not writable!");
        }
        $file = fopen($parsedArguments["stats"], "w");
        $arguments = $this->arguments2Array($parsedArguments);
        if ($file) {
            foreach ($arguments as $argument) {
                switch ($argument) {
                    case "labels":
                        fwrite($file, $this->labels . "\n");
                        break;
                    case "comments":
                        fwrite($file, $this->comments . "\n");
                        break;
                    case "loc":
                        fwrite($file, $this->loc . "\n");
                        break;
                    case "jumps":
                        fwrite($file, $this->jumps . "\n");
                        break;
                }
            }
        }
        fclose($file);
    }

    private function arguments2Array($parsedArguments) {
        global $argc;
        $newArguments = array_fill(0, $argc, "");
        foreach ($parsedArguments as $key => $argument) {
            switch ($key) {
                case "labels":
                case "comments":
                case "loc":
                case "jumps":
                    foreach ($argument as $order => $value)
                        $newArguments[$order] = $key;
            }
        }
        return array_filter($newArguments, array($this, 'checkEmpty'));
    }

    private function checkEmpty($argument){
        return !empty($argument);
    }

}


