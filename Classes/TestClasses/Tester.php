<?php

/**
 * @file    Tester.php
 * @class   Tester
 * @date    1.3.2020
 * @author  Martin Koči (xkocim05@stud.fit.vutbr.cz)
 * @brief   This class is testing all TestSuites and storing results.
 */
class Tester
{
    private int $mode;
    private string $parserFile;
    private string $interpretFile;
    private string $jExamXmlFile;
    private int $returnValue;

    private const PARSE_ONLY = 1;
    private const INT_ONLY = 2;
    private const BOTH = 3;

    /**
     * Tester constructor.
     * @param bool $parseOnly   if true sets mode for only parser testing cant combine with intOnly
     * @param bool $intOnly     if true sets mode for only interpret testing cant combine with parseOnly
     * @param string $parserFile    path to parse.php script
     * @param string $interpretFile path to interpret.php script
     * @param string $jExamXmlFile  path to jexamxml.jar file
     * @throws BadArgumentCombinationException
     * @brief Constructor sets the mode of the Tester base on given arguments.
     */
    public function __construct(bool $parseOnly, bool $intOnly, string $parserFile, string $interpretFile, string $jExamXmlFile) {
        if($parseOnly && !$intOnly) {
            $this->mode = self::PARSE_ONLY;
        } else if (!$parseOnly && $intOnly) {
            $this->mode = self::INT_ONLY;
        } else if (!$parseOnly && !$intOnly) {
            $this->mode = self::BOTH;
        } else {
            throw new BadArgumentCombinationException(basename(__FILE__)."::".__FUNCTION__." - You cant combine parserOnly with intOnly and reverse!");
        }
        $this->parserFile = $parserFile;
        $this->interpretFile = $interpretFile;
        $this->jExamXmlFile = $jExamXmlFile;
        $this->returnValue = 0;
    }

    /**
     * @param array $testSuites TestSuites that will be ran
     * @throws NotExistingFileException
     * @throws NotInstanceOfException
     * @throws PermissionException
     * @brief This method runs all testsSuites and storing results.
     */
    public function runTests(array $testSuites) {
        $this->checkFiles();
        foreach ($testSuites as $testSuite) {
            if (!$testSuite instanceof TestSuite) {
                throw new NotInstanceOfException(basename(__FILE__)."::".__FUNCTION__." - Object \"$testSuite\" is not instance of \"TestSuite\"");
            }
            $testSuite->checkAndCreateRequiredFiles();
            $testSuite->checkReadability();
            $testCases = $testSuite->getTestCases();
            switch($this->mode) {
                case self::PARSE_ONLY:
                    $this->parserOnly($testCases);
                    break;
                case self::INT_ONLY:
                    $this->interpretOnly($testCases);
                    break;
                case self::BOTH:
                    $this->both($testCases);
            }
        }
    }

    private function runParser(TestCase $testCase,string $tmpFile){
        $this->returnValue = $testCase->runParser($this->parserFile, $tmpFile);
        return (int)($this->returnValue == file_get_contents($testCase->getTestCaseRc()));
    }

    private function parserOnly(array $testCases) {
        $tmpFile = tempnam(sys_get_temp_dir(), "xml");
        foreach ($testCases as $testCase) {
            $hasPassed = $this->runParser($testCase, $tmpFile);
            if ($hasPassed && $this->returnValue != 0) {
                $testCase->setPassed();
            } else if ($hasPassed && $this->returnValue == 0) {
                if ($testCase->runJExamXml($this->jExamXmlFile, $tmpFile) == 0) {
                    $testCase->setPassed();
                }
            }
        }
        unlink($tmpFile);
    }

    private function runInterpret(TestCase $testCase, string $tmpInterpretOut, string $tmpInterpretIn = "") {
        $tmpXml = ($tmpInterpretIn == "") ? $testCase->getTestCaseSrc() : $tmpInterpretIn;
        $this->returnValue = $testCase->runInterpret($this->interpretFile, $tmpInterpretOut, $tmpXml);
        return (int)($this->returnValue == file_get_contents($testCase->getTestCaseRc()));
    }

    private function interpretOnly(array $testCases) {
        $tmpFile = tempnam(sys_get_temp_dir(), "interpret");
        foreach ($testCases as $testCase) {
            $hasPassed = $this->runInterpret($testCase, $tmpFile);
            if ($hasPassed && $this->returnValue != 0) {
                $testCase->setPassed();
            } else if ($hasPassed && $this->returnValue == 0) {
                if ($testCase->runDiff() == 0)
                    $testCase->setPassed();
            }
        }
        unlink($tmpFile);
    }

    private function both(array $testCases) {
        $tmpXml = tempnam(sys_get_temp_dir(), "xml");
        $tmpInterpret = tempnam(sys_get_temp_dir(), "interpret");
        foreach ($testCases as $testCase) {
            if ($testCase->runParser($this->parserFile, $tmpXml) == 0) {
                $hasPassed = $this->runInterpret($testCase, $tmpInterpret, $tmpXml);
                if ($hasPassed && $this->returnValue != 0) {
                    $testCase->setPassed();
                } else if ($hasPassed && $this->returnValue == 0) {
                    if ($testCase->runDiff($tmpInterpret) == 0)
                        $testCase->setPassed();
                }
            }
        }
        unlink($tmpXml);
        unlink($tmpInterpret);
    }

    private function checkFiles() {
        switch($this->mode) {
            case self::PARSE_ONLY:
                if(!is_file($this->jExamXmlFile) || !is_file($this->parserFile))
                    throw new NotExistingFileException(basename(__FILE__)."::".__FUNCTION__."  - File \"$this->jExamXmlFile\" or file \"$this->parserFile\" does not exist!");
                break;
            case self::INT_ONLY:
                if(!is_file($this->interpretFile))
                    throw new NotExistingFileException(basename(__FILE__)."::".__FUNCTION__."  - File \"$this->interpretFile\" does not exist!");
                break;
            case self::BOTH:
                if(!is_file($this->interpretFile) || !is_file($this->parserFile))
                    throw new NotExistingFileException(basename(__FILE__)."::".__FUNCTION__."  - File \"$this->interpretFile\" or file\"$this->parserFile\" does not exist!");
        }
    }
}
