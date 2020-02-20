<?php

    require_once "./Classes/ParserClasses/Analysis.php";
    require_once "./Classes/ParserClasses/Arguments.php";
    require_once "./Classes/ParserClasses/ArgvParser.php";
    require_once "./Classes/constants.php";
    require_once "./Classes/ParserClasses/FileManager.php";
    require_once "./Classes/ParserClasses/Instruction.php";
    require_once "./Classes/ParserClasses/Stats.php";
    require_once "./Classes/ParserClasses/XmlGenerator.php";
    require_once "./Classes/ParserClasses/CodeParser.php";

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
