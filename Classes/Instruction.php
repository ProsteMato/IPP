<?php


class Instruction
{
    private string $opCode;
    private array $arguments;

    public function __construct()
    {
        $this->opCode = "";
        $this->arguments = array();
    }

    public function getOpCode()
    {
        return $this->opCode;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function setOpCode(string $opCode)
    {
        $this->opCode = $opCode;
    }

    public function setArguments($arguments) {
        $this->arguments = $arguments;
    }
}
