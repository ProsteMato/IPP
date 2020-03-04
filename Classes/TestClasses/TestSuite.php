<?php

/**
 * @file    TestSuites.php
 * @class   TestSuites
 * @date    1.3.2020
 * @author  Martin Koči (xkocim05@stud.fit.vutbr.cz)
 * @brief   This class stores all information about TestSuite and TestCases and operations above them.
 */
class TestSuite
{
    private string $dirName;
    private array $testCases;

    /**
     * TestSuite constructor.
     * @param array $testCases  array of test cases.
     * @param string $dirName   Test Suite name.
     */
    public function __construct(array $testCases, string $dirName)
    {
        $this->dirName = $dirName;
        $this->testCases = $testCases;
    }

    /**
     * @return string return dirname of TestSuite
     */
    public function getDir()
    {
        return $this->dirName;
    }

    /**
     * @brief checks all Test Cases and they files and if some from req. files are missing generate it.
     */
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

    public function checkReadability() {
        foreach ($this->testCases as $testCase) {
            if (!is_readable($testCase->getTestCaseOut())) {
                throw new PermissionException(basename(__FILE__)."::".__FUNCTION__."File \"{$testCase->getTestCaseOut()}\" is not readable!");
            }
            if (!is_readable($testCase->getTestCaseIn())) {
                throw new PermissionException(basename(__FILE__)."::".__FUNCTION__."File \"{$testCase->getTestCaseIn()}\" is not readable!");
            }
            if (!is_readable($testCase->getTestCaseRc())) {
                throw new PermissionException(basename(__FILE__)."::".__FUNCTION__."File \"{$testCase->getTestCaseRc()}\" is not readable!");
            }
            if (!is_readable($testCase->getTestCaseSrc())) {
                throw new PermissionException(basename(__FILE__)."::".__FUNCTION__."File \"{$testCase->getTestCaseSrc()}\" is not readable!");
            }
        }
    }

    /**
     * @return array
     * @brief this method returns testCases
     */
    public function getTestCases() : array
    {
        return $this->testCases;
    }

    /**
     * @return int passed testCases
     */
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

    /**
     * @return int failed testCases
     */
    public function getTestCasesFailCount() : int
    {
        return count($this->testCases) - $this->getTestCasesPassCount();
    }
}
