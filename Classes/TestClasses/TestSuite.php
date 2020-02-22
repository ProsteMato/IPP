<?php


class TestSuite
{
    private string $dirName;
    private array $testCases;

    public function __construct($testCases, $dirName)
    {
        $this->dirName = $dirName;
        $this->testCases = $testCases;
    }

    public function getDir()
    {
        return $this->dirName;
    }

    public function checkAndCreateRequiredFiles()
    {
        foreach ($this->testCases as $testCase) {
            if (!is_file($testCase->getTestCaseOut())) {
                file_put_contents($testCase->getTestCaseOut(), "");
            }
            if (!is_file($testCase->getTestCaseIn())) {
                file_put_contents($testCase->getTestCaseIn(), "");
            }
            if (!is_file($testCase->getTestCaseRc())) {
                file_put_contents($testCase->getTestCaseRc(), "0");
            }
        }
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
