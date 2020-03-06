<?php

/**
 * @file    TestCase.php
 * @class   TestCase
 * @date    1.3.2020
 * @author  Martin Koči (xkocim05@stud.fit.vutbr.cz)
 * @brief   This class contains all information about testCase and do operations about running testCase
 */
class TestCase
{
    private string $fileName;
    private bool $passed;

    /**
     * TestCase constructor.
     * @param string $fileName fileName is name for the given testCase.
     */
    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
        $this->passed = false;
    }

    /**
     * @return string
     * @brief method returns filename with .src extension
     */
    public function getTestCaseSrc() {
        return $this->fileName . ".src";
    }

    /**
     * @return string
     * @brief method returns filename with .in extension
     */
    public function getTestCaseIn() {
        return $this->fileName . ".in";
    }

    /**
     * @return string
     * @brief method returns filename with .in extension
     */
    public function getTestCaseOut() {
        return $this->fileName . ".out";
    }

    /**
     * @return string
     * @brief method returns filename with .rc extension
     */
    public function getTestCaseRc() {
        return $this->fileName . ".rc";
    }

    /**
     * @return string
     * @brief method returns base name of filename
     */
    public function getBaseName() {
        return basename($this->fileName);
    }

    /**
     * @return bool if test passed or not
     */
    public function hasPassed() : bool
    {
        return $this->passed;
    }

    /**
     * @brief Set test case true that passed.
     */
    public function setPassed()
    {
        $this->passed = true;
    }
}
