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
    private TestCase $actualTestCase;
    private string $interpretFile;
    private string $jExamXmlFile;
    private string $parserFile;
    private string $tmpFile;
    private string $tmpFileInterpret;
    private int $returnValue;
    private int $mode;

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
        $this->tmpFile = tempnam(sys_get_temp_dir(), "tmpFile");
        $this->tmpFileInterpret = tempnam(sys_get_temp_dir(), "interpret");
    }

    public function __destruct()
    {
        unlink($this->tmpFile);
        unlink($this->tmpFileInterpret);
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

    private function compareOutputs(callable $compareFunc) {
        if ($this->returnValue == 0) {
            if (call_user_func($compareFunc) == 0)
                $this->actualTestCase->setPassed();
        } else {
            $this->actualTestCase->setPassed();
        }
    }

    private function parserOnly(array $testCases) {
        foreach ($testCases as $this->actualTestCase) {
            if ($this->runParser()) {
                $this->compareOutputs(array($this, 'runJExamXml'));
            }
        }
    }

    private function interpretOnly(array $testCases) {
        foreach ($testCases as $this->actualTestCase) {
            if($this->runInterpret($this->actualTestCase->getTestCaseSrc())) {
                $this->compareOutputs(array($this, "runDiff"));
            }
        }
    }

    private function both(array $testCases) {
        foreach ($testCases as $this->actualTestCase) {
            $this->runParser();
            if ($this->returnValue == 0) {
                if($this->runInterpret($this->tmpFile)) {
                    $this->compareOutputs(array($this, "runDiff"));
                }
            }
        }
    }

    private function runParser() {
        system(
            "php7.4 \"$this->parserFile\" < \"{$this->actualTestCase->getTestCaseSrc()}\" > \"$this->tmpFile\"",
            $this->returnValue
        );
        return (int)($this->returnValue == file_get_contents($this->actualTestCase->getTestCaseRc()));
    }

    private function runInterpret($source) {
        system(
            "python3.8 \"$this->interpretFile\" --source=\"$source\" --input=\"{$this->actualTestCase->getTestCaseIn()}\" > \"$this->tmpFileInterpret\"",
            $this->returnValue
        );
        return (int)($this->returnValue == file_get_contents($this->actualTestCase->getTestCaseRc()));
    }

    private function runJExamXml() {
        $tmpDiff = tempnam(sys_get_temp_dir(), "diff");
        system(
            "java -jar \"$this->jExamXmlFile\" \"{$this->actualTestCase->getTestCaseOut()}\" \"$this->tmpFile\" \"$tmpDiff\" \D \"".dirname($this->jExamXmlFile)."/options\" > /dev/null",
            $this->returnValue
        );
        unlink($tmpDiff);
        return $this->returnValue;
    }

    private function runDiff() {
        system(
            "diff \"$this->tmpFileInterpret\" \"{$this->actualTestCase->getTestCaseOut()}\" > /dev/null",
            $this->returnValue
        );
        return $this->returnValue;
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
