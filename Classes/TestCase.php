<?php


class TestCase
{
    private string $fileName;
    private bool $passed;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
        $this->passed = false;
    }

    public function hasPassed() : bool
    {
        return $this->passed;
    }

    public function setPassed()
    {
        $this->passed = true;
    }

    public function getFile() : string
    {
        return $this->fileName;
    }
}
