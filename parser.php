<?php

    require_once "./Classes/Analysis.php";
    require_once "./Classes/Arguments.php";
    require_once "./Classes/ArgvParser.php";
    require_once "./Classes/constants.php";
    require_once "./Classes/FileManager.php";
    require_once "./Classes/Instruction.php";
    require_once "./Classes/Stats.php";
    require_once "./Classes/XmlGenerator.php";
    require_once "./Classes/CodeParser.php";

    function printHelp() {
        echo HELP;
        exit(0);
    }

    $stats = new Stats();
    $fileManager = new FileManager(STDIN, $stats);
    $argParser = new ArgvParser();
    $analysis = new Analysis($stats);
    $xmlGenerator = new XmlGenerator();
    $instruction = new Instruction();
    $parser = new CodeParser($analysis, $xmlGenerator, $fileManager, $instruction, $stats);

    try {
        $parsedArguments = $argParser->parseArgv($argv, $argc);
        if(in_array("help", $parsedArguments))
            printHelp();
        $parser->parse();
        if(array_key_exists("stats", $parsedArguments)) {
            $stats->generateStats($parsedArguments);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        exit($e->getCode());
    }
