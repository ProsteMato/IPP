<?php


class TestCase
{
    private string $fileName;
    private bool $passed;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
        $this->passed = false;
    }

    public function getTestCaseSrc() {
        if(!is_file($this->fileName . ".src")) {
            throw new NotExistingFileException("test.php::TestCase - File \"$this->fileName.src\" does not exist!");
        }
        return $this->fileName . ".src";
    }

    public function getTestCaseIn() {
        if(!is_file($this->fileName . ".in")) {
            throw new NotExistingFileException("test.php::TestCase - File \"$this->fileName.in\" does not exist!");
        }
        return $this->fileName . ".In";
    }

    public function getTestCaseOut() {
        if(!is_file($this->fileName . ".out")) {
            throw new NotExistingFileException("test.php::TestCase - File \"$this->fileName.out\" does not exist!");
        }
        return $this->fileName . ".out";
    }

    public function getTestCaseRc() {
        if(!is_file($this->fileName . ".rc")) {
            throw new NotExistingFileException("test.php::TestCase - File \"$this->fileName.rc\" does not exist!");
        }
        return $this->fileName . ".rc";
    }

    public function getBaseName() {
        return basename($this->fileName);
    }

    public function hasPassed() : bool
    {
        return $this->passed;
    }

    public function setPassed()
    {
        $this->passed = true;
    }

    public function runParser($file, $tmpFile) {
        $returnValue = 0;
        system(
            "php7.4 $file < {$this->getTestCaseSrc()} > $tmpFile",
            $returnValue
        );
        return $returnValue;
    }

    public function runJExamXml($file, $tmpFile) {
        $returnValue = 0;
        $tmpDiff = tempnam(sys_get_temp_dir(), "diff");
        system(
            "java -jar $file {$this->getTestCaseOut()} $tmpFile $tmpDiff \D ".dirname($file)."/options > /dev/null",
            $returnValue
        );
        unlink($tmpDiff);
        return $returnValue;
    }

    public function runInterpret($file, $tmpFile, $source) {
        $returnValue = 0;
        system(
            "python3 $file --source=$source --input={$this->getTestCaseIn()} > $tmpFile",
            $returnValue
        );
        return $returnValue;
    }

    public function runDiff($tmpFile) {
        $returnValue = 0;
        system(
            "diff $tmpFile {$this->getTestCaseOut()} > /dev/null",
            $returnValue
        );
        return $returnValue;
    }
}
