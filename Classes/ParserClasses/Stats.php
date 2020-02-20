<?php


class Stats
{
    private $loc;
    private $comments;
    private $labels;
    private $jumps;

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

    public function generateStats($parsedArguments) {
        if (strcmp($parsedArguments["stats"], "") == 0)
            throw new Exception("Filename can not be empty", Errors::BAD_ARGUMENT);
        $file = fopen($parsedArguments["stats"], "w");
        unset($parsedArguments["stats"]);
        if($file) {
            foreach ($parsedArguments as $argument) {
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
}
