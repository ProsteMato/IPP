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

    /**
     * @param string $file path to parse.php
     * @param string $tmpFile filename of tmpFile
     * @return int  return value from parser.
     * @brief run test in parse.php script.
     */
    public function runParser($file, $tmpFile) {
        $returnValue = 0;
        system(
            "php7.4 \"$file\" < \"{$this->getTestCaseSrc()}\" > \"$tmpFile\"",
            $returnValue
        );
        return $returnValue;
    }

    /**
     * @param string $file path to jexamxml
     * @param string $tmpFile filename of tmp result from parser
     * @return int  return value from jexamxml.
     * @brief run JExamXml comparison on Test Case output file and tmpFile
     */
    public function runJExamXml($file, $tmpFile) {
        $returnValue = 0;
        $tmpDiff = tempnam(sys_get_temp_dir(), "diff");
        system(
            "java -jar \"$file\" \"{$this->getTestCaseOut()}\" \"$tmpFile\" \"$tmpDiff\" \D \"".dirname($file)."/options\" > /dev/null",
            $returnValue
        );
        unlink($tmpDiff);
        return $returnValue;
    }

    /**
     * @param string $file path to interpreter.py
     * @param string $tmpFile tmpFile result from interpreter.py
     * @param string $source  input file from parser or .src file from test case
     * @return int return value from interpreter.py
     * @brief run test in interpreter.py script.
     */
    public function runInterpret($file, $tmpFile, $source) {
        $returnValue = 0;
        system(
            "python3 \"$file\" --source=\"$source\" --input=\"{$this->getTestCaseIn()}\" > \"$tmpFile\"",
            $returnValue
        );
        return $returnValue;
    }

    /**
     * @param string $tmpFile filename of file where result of interpreter.py is.
     * @return int return value from diff
     * @brief run diff on $tmpFile and Test Case .out file.
     */
    public function runDiff($tmpFile) {
        $returnValue = 0;
        system(
            "diff \"$tmpFile\" \"{$this->getTestCaseOut()}\" > /dev/null",
            $returnValue
        );
        return $returnValue;
    }
}
