<?php


class FileAdministrator
{
    private array $dirs;
    private array $testSuites;
    private bool $recursive;

    public function __construct(array $dirs, bool $recursive)
    {
        $this->dirs = $dirs;
        $this->recursive = $recursive;
        $this->testSuites = array();
    }

    public function getTestSuites() : array
    {
        $tests = array();
        foreach ($this->dirs as $dir)
        {
            $currentDir = new RecursiveDirectoryIterator($dir);
            $currentDir->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
            $currentDirIter = new RecursiveIteratorIterator($currentDir);
            if (!$this->recursive)
                $currentDirIter->setMaxDepth("0");
            foreach ($currentDirIter as $path)
            {
                if(!array_key_exists(dirname($path), $tests))
                    $tests[dirname($path)] = array();
                if(preg_match("/.*src/", $path))
                    array_push($tests[dirname($path)], basename($path, ".src"));
            }
        }

        foreach ($tests as $testSuitName => $testCaseNames)
        {
            $testsCases = array();
            foreach ($testCaseNames as $testCaseName)
            {
                array_push($testsCases, new TestCase($testCaseName));
            }
            array_push($this->testSuites, new TestSuite($testSuitName, $testsCases));
        }

        print_r($this->testSuites);
        return $this->testSuites;
    }
}