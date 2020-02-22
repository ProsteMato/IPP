<?php


class Tester
{
    private int $mode;
    private string $parserFile;
    private string $interpretFile;
    private string $jExamXmlFile;

    const PARSE_ONLY = 1;
    const INT_ONLY = 2;
    const BOTH = 3;

    public function __construct(bool $parseOnly, bool $intOnly, string $parserFile, string $interpretFile, string $jExamXmlFile) {
        if($parseOnly && !$intOnly) {
            $this->mode = self::PARSE_ONLY;
        } else if (!$parseOnly && $intOnly) {
            $this->mode = self::INT_ONLY;
        } else if (!$parseOnly && !$intOnly) {
            $this->mode = self::BOTH;
        } else {
            throw new BadArgumentCombinationException("You cant combine parserOnly with intOnly and reverse!");
        }
        $this->parserFile = $parserFile;
        $this->interpretFile = $interpretFile;
        $this->jExamXmlFile = $jExamXmlFile;
    }

    public function runTests(array $testSuites) {
        $this->checkFiles();
        foreach ($testSuites as $testSuite) {
            if (!$testSuite instanceof TestSuite) {
                throw new NotInstanceOfException("\"$testSuite\" is not instance of \"TestSuite\"");
            }
            $testSuite->checkAndCreateRequiredFiles();
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

    private function parserOnly(array $testCases) {
        $tmpFile = tempnam(sys_get_temp_dir(), "xml");
        foreach ($testCases as $testCase) {
            if ($testCase->runParser($this->parserFile, $tmpFile) == file_get_contents($testCase->getTestCaseRc())) {
                if($testCase->runJExamXml($this->jExamXmlFile, $tmpFile) == 0) {
                    $testCase->setPassed();
                }
            }
        }
        unlink($tmpFile);
    }

    private function interpretOnly(array $testCases) {
        $tmpFile = tempnam(sys_get_temp_dir(), "interpret");
        foreach ($testCases as $testCase) {
            $returnValue = $testCase->runInterpret($this->interpretFile, $tmpFile, $testCase->getTestCaseSrc());
            if($returnValue == file_get_contents($testCase->getTestCaseRc())) {
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
                $returnValue = $testCase->runInterpret($this->interpretFile, $tmpInterpret, $tmpXml);
                if($returnValue == file_get_contents($testCase->getTestCaseRc())) {
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
                    throw new NotExistingFileException("\"$this->jExamXmlFile\" or \"$this->parserFile\" does not exist!");
                break;
            case self::INT_ONLY:
                if(!is_file($this->interpretFile))
                    throw new NotExistingFileException("\"$this->interpretFile\" does not exist!");
                break;
            case self::BOTH:
                if(!is_file($this->interpretFile) || !is_file($this->parserFile))
                    throw new NotExistingFileException("\"$this->interpretFile\" or \"$this->parserFile\" does not exist!");
        }
    }
}
