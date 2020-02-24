<?php

    require_once "./Classes/ParserClasses/Analysis.php";
    require_once "./Classes/ParserClasses/Arguments.php";
    require_once "./Classes/constants.php";
    require_once "./Classes/ParserClasses/FileManager.php";
    require_once "./Classes/ParserClasses/Instruction.php";
    require_once "./Classes/ParserClasses/Stats.php";
    require_once "./Classes/ParserClasses/XmlGenerator.php";
    require_once "./Classes/ParserClasses/CodeParser.php";
    require_once "./Classes/TestClasses/ArgParser.php";


    function printHelp() {
        echo HELP;
        exit(Errors::ERR_OK);
    }

    $argParser = (new ArgParser())
        ->addArgument("stats", true)
        ->addRepeatableArgument("loc", false, null, array(), array("stats"))
        ->addRepeatableArgument("jumps", false, null, array(), array("stats"))
        ->addRepeatableArgument("comments", false, null, array(), array("stats"))
        ->addRepeatableArgument("labels", false , null, array(), array("stats"));

    try {
        $parsedArguments = $argParser->parseArguments();
        if (isset($parsedArguments["help"]))
            printHelp();
        $stats = new Stats();
        $fileManager = new FileManager(STDIN, $stats);
        $analysis = new Analysis($stats);
        $xmlGenerator = new XmlGenerator();
        $instruction = new Instruction();
        $parser = new CodeParser($analysis, $xmlGenerator, $fileManager, $instruction, $stats);
        $parser->parse();
        if(isset($parsedArguments["stats"])) {
            $stats->generateStats($parsedArguments);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        exit($e->getCode());
    }
