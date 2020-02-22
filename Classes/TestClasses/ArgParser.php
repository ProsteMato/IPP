<?php


class ArgParser
{
    private array $longOptions;
    private array $required;
    private array $defaultValue;
    private array $invalidCombinations;
    private array $parsedOptions;

    public function __construct()
    {
        $this->longOptions = array();
        $this->required = array();
        $this->parsedOptions = array();
        $this->invalidCombinations = array();
        $this->invalidCombinations["help"] = array();
        $this->required["help"] = false;
        $this->parsedOptions = array();
    }

    public function parseArguments()
    {
        $this->parse();
        $this->checkInvalidCombinations();
        $this->setDefaultValue();
        return $this->parsedOptions;
    }

    public function addArgument(string $longOption, bool $expectValue, $defaultValue = null, array $invalidCombinations = array()) {
        array_push($this->longOptions, $longOption);
        array_push($this->invalidCombinations["help"], $longOption);
        $this->required[$longOption] = $expectValue;
        $this->defaultValue[$longOption] = $defaultValue;
        $this->invalidCombinations[$longOption] = $invalidCombinations;
        return $this;
    }

    private function parse() {
        global $argv, $argc;
        for ($i = 1; $i < $argc; $i++) {
            $splitArgument = preg_split("/[=]/", $argv[$i]);
            if (!preg_match("/^--.*/", $splitArgument[0])) {
                throw new UndefinedArgumentException("Invalid argument \"$splitArgument[0]\"");
            }
            $splitArgument[0] = preg_replace("/^--/", "", $splitArgument[0]);
            $this->checkArgument($splitArgument);
            $this->createArgument($splitArgument);
        }
    }

    private function checkArgument(array $splitArgument)
    {
        if(!in_array($splitArgument[0], $this->longOptions) && strcmp("help", $splitArgument[0]) != 0) {
            throw new UndefinedArgumentException("Argument \"$splitArgument[0]\" is not defined!");
        }
        if ($this->required[$splitArgument[0]] && !key_exists(1, $splitArgument)) {
            throw new RequiredValueException("Argument \"$splitArgument[0]\" have required value!");
        }
    }

    private function createArgument(array $splitArgument)
    {
        if ($this->required[$splitArgument[0]]) {
            if (key_exists($splitArgument[0], $this->parsedOptions)) {
                throw new RedefinitionOfArgumentException("Argument $splitArgument[0] is already defined!");
            }
            $this->parsedOptions[$splitArgument[0]] = $splitArgument[1];
        } else {
            $this->parsedOptions[$splitArgument[0]] = true;
        }
    }

    private function checkInvalidCombinations() {
        foreach (array_keys($this->parsedOptions) as $option) {
            foreach ($this->invalidCombinations[$option] as $invalidCombination) {
                if (isset($this->parsedOptions[$invalidCombination])){
                    throw new BadArgumentCombinationException("Argument \"$option\" and argument \"$invalidCombination\" cant be used together!");
                }
            }
        }
    }

    private function setDefaultValue() {
        foreach ($this->longOptions as $option) {
            if(!isset($this->parsedOptions[$option]) && isset($this->defaultValue[$option]))
                $this->parsedOptions[$option] = $this->defaultValue[$option];
        }
    }

}