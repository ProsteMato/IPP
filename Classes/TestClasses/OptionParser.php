<?php

//TODO pridat parameter pre vlakladanie non-required parametrov do hodnot
class OptionParser
{
    private array $longOptions;
    private array $required;
    private array $parsedOptions;

    public function __construct($longOptions)
    {
        $this->longOptions = array();
        $this->required = array();
        $this->parsedOptions = array();
        foreach ($longOptions as $longOption)
        {
            $parsedOption = preg_replace("/[:]$/", "", $longOption);
            if(preg_match("/[:]$/", $longOption))
            {
                array_push($this->required, $parsedOption);
            }
            array_push($this->longOptions, $parsedOption);
        }
    }

    private function checkArgument(array $splitArgument)
    {
        if(!in_array($splitArgument[0], $this->longOptions)) {
            throw new UndefinedArgumentException("$splitArgument[0] is undefined!");
        }
        if (in_array($splitArgument[0], $this->required) && !key_exists(1, $splitArgument)) {
            throw new RequiredValueException("$splitArgument[0] have required value!");
        }
    }

    private function addArgument(array $splitArgument)
    {
        if (in_array($splitArgument[0], $this->required)) {
            if(key_exists($splitArgument[0], $this->parsedOptions)) {
                throw new RedefinitionOfArgumentException("Argument $splitArgument[0] is already defined!");
            }
            $this->parsedOptions[$splitArgument[0]] = $splitArgument[1];
        } else {
            $this->parsedOptions[$splitArgument[0]] = true;
        }
    }

    public function parseArgv()
    {
        global $argv, $argc;
        for ($i = 1; $i < $argc; $i++)
        {
            $splitArgument = preg_split("/[=]/", $argv[$i]);
            if (!preg_match("/^--.*/", $splitArgument[0])) {
                throw new UndefinedArgumentException("Bad argument \"$splitArgument[0]\"");
            }
            $splitArgument[0] = preg_replace("/^--/", "", $splitArgument[0]);
            $this->checkArgument($splitArgument);
            $this->addArgument($splitArgument);
        }
        return $this->parsedOptions;
    }

}