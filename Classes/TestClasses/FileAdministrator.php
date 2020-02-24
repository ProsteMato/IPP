<?php


class FileAdministrator
{
    private string $dirs;
    private array $testSuites;
    private bool $recursive;

    public function __construct(string $dir, bool $recursive)
    {
        if (!file_exists($dir)) {
            throw new NotExistingFileException(basename(__FILE__)."::".__FUNCTION__." - Directory \"$dir\" does not exist!");
        }
        $this->dirs = $dir;
        $this->recursive = $recursive;
        $this->testSuites = array();
    }

    public function getTestSuites() : array
    {
        $currentDir = new RecursiveDirectoryIterator($this->dirs);
        $currentDir->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
        $currentDirIter = new RecursiveIteratorIterator($currentDir);
        if (!$this->recursive)
            $currentDirIter->setMaxDepth("0");
        $tests = $this->getTestCasesFromIterator($currentDirIter);
        $this->createTestSuite($tests);
        return $this->testSuites;
    }

    private function getTestCasesFromIterator($currentDirIter) : array
    {
        $tests = array();
        foreach ($currentDirIter as $path)
        {
            if(!array_key_exists(dirname($path), $tests))
                $tests[dirname($path)] = array();
            if(preg_match("/.*src/", $path))
                array_push($tests[dirname($path)], dirname($path) ."/". basename($path, ".src"));
        }
        return $tests;
    }

    private function createTestSuite($tests)
    {
        foreach ($tests as $testSuitName => $testCaseNames)
        {
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