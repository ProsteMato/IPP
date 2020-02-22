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

    public function hasPassed() : bool
    {
        return $this->passed;
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

    public function getTestCaseSrc() {
        return $this->fileName . ".src";
    }

    public function getTestCaseIn() {
        return $this->fileName . ".in";
    }

    public function getTestCaseOut() {
        return $this->fileName . ".out";
    }

    public function getTestCaseRc() {
        return $this->fileName . ".rc";
    }

    public function getBaseName() {
        return basename($this->fileName);
    }

    public function setPassed()
    {
        $this->passed = true;
    }
}
