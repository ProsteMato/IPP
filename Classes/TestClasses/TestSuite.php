<?php


class TestSuite
{
    private string $dirName;
    private array $testCases;

    public function __construct($testCases)
    {
        $this->testCases = $testCases;
    }

    public function getDirName() : string
    {
        return $this->dirName;
    }

    public function getTestCases() : array
    {
        return $this->testCases;
    }

    public function getTestCasesPassCount() : int
    {
        $passed = 0;
        foreach ($this->testCases as $testCase) {
            if($testCase->hasPassed()){
                $passed++;
            }
        }
        return $passed;
    }

    public function getTestCasesFailCount() : int
    {
        return count($this->testCases) - $this->getTestCasesPassCount();
    }
}
