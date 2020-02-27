<?php


class FileAdministrator
{
    private string $file;
    private array $testSuites;
    private array $testCases;
    private string $regex;
    private array $files;
    private bool $recursive;

    public function __construct(string $file, bool $recursive, string $regex) {
        if(preg_match($regex, null) === false) {
            throw new NotExistingFileException(basename(__FILE__)."::".__FUNCTION__." - $regex is not a valid regular expression");
        }
        if (!file_exists($file)) {
            throw new NotExistingFileException(basename(__FILE__)."::".__FUNCTION__." - Directory \"$file\" does not exist!");
        }
        $this->file = $file;
        $this->recursive = $recursive;
        $this->regex = $regex;
        $this->testSuites = array();
        $this->testCases = array();
        $this->files = array();

    }

    public function getTestSuites() : array {
        $this->getFiles();
        foreach ($this->files as $file) {
            $file = trim($file);
            $currentDir = new RecursiveDirectoryIterator($file);
            $currentDir->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
            $currentDirIter = new RecursiveIteratorIterator($currentDir);
            if (!$this->recursive)
                $currentDirIter->setMaxDepth("0");
            $this->getTestCasesFromIterator($currentDirIter);
        }
        $this->createTestSuite();
        return $this->testSuites;
    }

    private function getTestCasesFromIterator($currentDirIter) {
        foreach ($currentDirIter as $path) {
            $this->addTestCase($path);
        }
    }

    private function addTestCase($file) {
        $pathInfo = pathinfo($file);
        $testCase = $pathInfo["dirname"] ."/". $pathInfo["filename"];
        if(!array_key_exists($pathInfo["dirname"], $this->testCases))
            $this->testCases[$pathInfo["dirname"]] = array();
        if(preg_match($this->regex, $file) && !in_array($testCase, $this->testCases[$pathInfo["dirname"]]))
            array_push($this->testCases[$pathInfo["dirname"]], $testCase);
    }

    private function getFiles() {
        if(is_dir($this->file)) {
           array_push($this->files, $this->file);
        } else if(is_file($this->file)) {
            $this->files = file($this->file);
            $this->filterFiles();
        }
    }

    private function filterFiles() {
        foreach ($this->files as $key => $file) {
            $file = trim($file);
            if (is_file($file) && preg_match($this->regex, $file)) {
                $this->addTestCase($file);
                unset($this->files[$key]);
            } else if (!is_dir($file)) {
                throw new NotExistingFileException(basename(__FILE__)."::".__FUNCTION__." - Directory \"$file\" does not exist!");
            }
        }
    }

    private function createTestSuite() {
        foreach ($this->testCases as $testSuitName => $testCaseNames) {
            $testsCases = $this->createTestCases($testCaseNames);
            if(!array_key_exists($testSuitName, $this->testSuites))
                $this->testSuites[$testSuitName] = new TestSuite($testsCases, $testSuitName);
        }
    }

    private function createTestCases($testCaseNames) : array {
        $testsCases = array();
        foreach ($testCaseNames as $testCaseName)
        {
            $testsCases[$testCaseName] = new TestCase($testCaseName);
        }
        return $testsCases;
    }
}