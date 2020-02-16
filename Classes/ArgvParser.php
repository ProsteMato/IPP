<?php


class ArgvParser {
    private array $parsedArguments;

    public function __construct()
    {
        $this->parsedArguments = array();
    }

    public function parseArgv($argv, $argc)
    {

        for($index = 1; $index < $argc; $index++) {
            $splitArg = preg_split("/=/", $argv[$index], 2);
            $this->checkArgvAssignParam($splitArg);
            switch ($splitArg[0]) {
                case "--help":
                    array_push($this->parsedArguments, "help");
                    break;
                case "--stats":
                    if (array_key_exists("stats", $splitArg))
                        throw new Exception("More than one output file for stats!", Errors::BAD_ARGUMENT);
                    $this->parsedArguments["stats"] = $splitArg[1];
                    break;
                case "--loc":
                    array_push($this->parsedArguments, "loc");
                    break;
                case "--comments":
                    array_push($this->parsedArguments, "comments");
                    break;
                case "--jumps":
                    array_push($this->parsedArguments, "jumps");
                    break;
                case "--labels":
                    array_push($this->parsedArguments, "labels");
                    break;
                default:
                    throw new Exception("Undefined input argument", Errors::BAD_ARGUMENT);
                    break;
            }
        }
        if (!$this->checkCorrectionOfArgv($argc)) {
            throw new Exception("Bad input argument", Errors::BAD_ARGUMENT);
        }
        return $this->parsedArguments;
    }

    private function checkArgvAssignParam($splitArg) {
        if (array_key_exists(1, $splitArg) && $splitArg[0] != "--stats") {
            throw new Exception("Assign not allowed!", Errors::BAD_ARGUMENT);
        }
        if (!array_key_exists(1, $splitArg) && $splitArg[0] == "--stats") {
            throw new Exception("Missing Assign on stats argv!", Errors::BAD_ARGUMENT);
        }
    }

    private function checkCorrectionOfArgv($argc)
    {
        if (in_array("help", $this->parsedArguments) && $argc != 2)
            return false;
        if (!array_key_exists("stats", $this->parsedArguments) && !in_array("help", $this->parsedArguments) && $argc != 1)
            return false;
        return true;
    }
}
