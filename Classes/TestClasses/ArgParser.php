<?php


class ArgParser
{
    private array $longOptions;
    private array $required;
    private array $defaultValue;
    private array $invalidCombinations;
    private array $requiredCombination;
    private array $parsedOptions;
    private array $repeatableArguments;
    private int $order;

    public function __construct()
    {
        $this->longOptions = array();
        $this->required = array();
        $this->parsedOptions = array();
        $this->repeatableArguments = array();
        $this->invalidCombinations = array();
        $this->invalidCombinations["help"] = array();
        $this->requiredCombination = array();
        $this->requiredCombination["help"] = array();
        $this->repeatableArguments["help"] = false;
        $this->required["help"] = false;
        $this->parsedOptions = array();
        $this->order = 0;
    }

    public function parseArguments()
    {
        $this->createArguments();
        foreach (array_keys($this->parsedOptions) as $option) {
            $this->checkInvalidCombinations($option);
            $this->checkRequiredCombinations($option);
        }
        $this->setDefaultValue();
        return $this->parsedOptions;
    }

    public function addArgument(string $longOption, bool $expectValue, $defaultValue = null,
                                array $invalidCombinations = array(), array $requiredCombination = array()) {
        $this->repeatableArguments[$longOption] = false;
        $this->setArgumentParameters($longOption, $expectValue, $defaultValue, $invalidCombinations, $requiredCombination);
        return $this;
    }

    public function addRepeatableArgument(string $longOption, bool $expectValue, $defaultValue = null,
                                          array $invalidCombinations = array(), array $requiredCombination = array()) {
        $this->repeatableArguments[$longOption] = true;
        $this->setArgumentParameters($longOption, $expectValue, $defaultValue, $invalidCombinations, $requiredCombination);
        return $this;
    }

    private function setArgumentParameters(string $longOption, bool $expectValue, $defaultValue = null,
                                           array $invalidCombinations = array(), array $requiredCombination = array()) {
        if (in_array($longOption, $this->longOptions))
            throw new InternalRedefinationOfArgumentException(basename(__FILE__)."::".__FUNCTION__." - Argument \"$longOption\" is already defined!");
        array_push($this->longOptions, $longOption);
        array_push($this->invalidCombinations["help"], $longOption);
        $this->required[$longOption] = $expectValue;
        $this->defaultValue[$longOption] = $defaultValue;
        $this->invalidCombinations[$longOption] = $invalidCombinations;
        $this->requiredCombination[$longOption] = $requiredCombination;
    }

    private function createArguments() {
        global $argv, $argc;
        for ($i = 1; $i < $argc; $i++) {
            $splitArgument = $this->validateArgument($argv[$i]);
            $this->checkValueArgument($splitArgument);
            $this->createArgument($splitArgument);
        }
    }

    private function validateArgument($argument) {
        $splitArgument = preg_split("/[=]/", $argument);
        if (!preg_match("/^--.*/", $splitArgument[0])) {
            throw new UndefinedArgumentException(basename(__FILE__)."::".__FUNCTION__." - Invalid argument \"$splitArgument[0]\"");
        }
        $splitArgument[0] = preg_replace("/^--/", "", $splitArgument[0]);
        return $splitArgument;
    }

    private function checkValueArgument(array $splitArgument)
    {
        if (!$this->isArgument($splitArgument[0]) && strcmp("help", $splitArgument[0]) != 0) {
            throw new UndefinedArgumentException(basename(__FILE__)."::".__FUNCTION__." - Argument \"$splitArgument[0]\" is not defined!");
        }
        if ($this->required[$splitArgument[0]] && (!key_exists(1, $splitArgument) || $splitArgument[1] === "")) {
            throw new RequiredValueException(basename(__FILE__)."::".__FUNCTION__." - Argument \"$splitArgument[0]\" have required value!");
        }
    }

    private function createArgument(array $splitArgument) {
        if($this->isRepeatable($splitArgument[0])) {
            $this->createRepeatableArgument($splitArgument);
        } else {
            $this->createNoRepeatableArgument($splitArgument);
        }

    }

    private function createNoRepeatableArgument(array $splitArgument) {
        if ($this->required[$splitArgument[0]]) {
            if (key_exists($splitArgument[0], $this->parsedOptions)) {
                throw new RedefinitionOfArgumentException(basename(__FILE__)."::".__FUNCTION__." - Argument $splitArgument[0] is already defined!");
            }
            $this->parsedOptions[$splitArgument[0]] = $splitArgument[1];
        } else {
            $this->parsedOptions[$splitArgument[0]] = true;
        }
    }

    private function createRepeatableArgument(array $splitArgument) {
        if (!key_exists($splitArgument[0], $this->parsedOptions))
            $this->parsedOptions[$splitArgument[0]] = array();
        if ($this->required[$splitArgument[0]]) {
            array_push($this->parsedOptions[$splitArgument[0]], $splitArgument[0]);
        } else {
            array_push($this->parsedOptions[$splitArgument[0]], $this->order);
            $this->order++;
        }
    }

    private function checkInvalidCombinations($option) {
        foreach ($this->invalidCombinations[$option] as $invalidCombination) {
            if (isset($this->parsedOptions[$invalidCombination])){
                throw new BadArgumentCombinationException(basename(__FILE__)."::".__FUNCTION__." - Argument \"$option\" and argument \"$invalidCombination\" cant be used together!");
            }
        }
    }

    private function checkRequiredCombinations($option) {
        foreach ($this->requiredCombination[$option] as $requiredCombination) {
            if (!isset($this->parsedOptions[$requiredCombination])){
                throw new BadArgumentCombinationException(basename(__FILE__)."::".__FUNCTION__." - Argument \"$requiredCombination\" must be set when you want to use argument \"$option\"!");
            }
        }
    }

    private function setDefaultValue() {
        foreach ($this->longOptions as $option) {
            if(!isset($this->parsedOptions[$option]) && isset($this->defaultValue[$option]))
                $this->parsedOptions[$option] = $this->defaultValue[$option];
        }
    }

    private function isRepeatable($argument) {
        return $this->repeatableArguments[$argument];
    }

    private function isArgument($argument) {
        return in_array($argument, $this->longOptions);
    }
}