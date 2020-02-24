<?php


class Stats
{
    private int $loc;
    private int $comments;
    private int $labels;
    private int $jumps;

    public function __construct()
    {
        $this->loc = 0;
        $this->jumps = 0;
        $this->labels = 0;
        $this->comments = 0;
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

    public function generateStats($parsedArguments)
    {
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


