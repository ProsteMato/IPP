<?php
/**
 * @file    parser.php
 * @date    1.3.2020
 * @author  Martin Koči (xkocim05@stud.fit.vutbr.cz)
 * @brief   This script will parse IPPcode20 src files to XML representation.
 */

require_once "./Classes/ParserClasses/Analysis.php";
require_once "./Classes/ParserClasses/Arguments.php";
require_once "./Classes/constants.php";
require_once "./Classes/ParserClasses/FileManager.php";
require_once "./Classes/ParserClasses/Instruction.php";
require_once "./Classes/ParserClasses/Stats.php";
require_once "./Classes/ParserClasses/XmlGenerator.php";
require_once "./Classes/ParserClasses/CodeParser.php";
require_once "./Classes/TestClasses/ArgParser.php";
require_once "./Classes/ExceptionClasses/InvalidInstructionException.php";
require_once "./Classes/ExceptionClasses/InvalidHeaderException.php";
require_once "./Classes/ExceptionClasses/ArgumentException.php";
require_once "./Classes/ExceptionClasses/InternalException.php";
require_once "./Classes/ExceptionClasses/BadArgumentCombinationException.php";
require_once "./Classes/ExceptionClasses/InternalRedefinitionOfArgumentException.php";
require_once "./Classes/ExceptionClasses/UndefinedArgumentException.php";
require_once "./Classes/ExceptionClasses/RedefinitionOfArgumentException.php";
require_once "./Classes/ExceptionClasses/RequiredValueException.php";


function printHelp() {
    echo PARSER_HELP;
    exit(Errors::ERR_OK);
}

try {
    $argParser = (new ArgParser())
        ->addArgument("stats", true)
        ->addRepeatableArgument("loc", false, null, array(), array("stats"))
        ->addRepeatableArgument("jumps", false, null, array(), array("stats"))
        ->addRepeatableArgument("comments", false, null, array(), array("stats"))
        ->addRepeatableArgument("labels", false , null, array(), array("stats"));
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
} catch (InvalidArgumentException $e) {
    error_log($e->getMessage());
    exit(Errors::LEX_OR_SYNTAX_ERR);
} catch (InvalidHeaderException $e) {
    error_log($e->getMessage());
    exit(Errors::INSTRUCTION_ERR);
} catch (InvalidInstructionException $e) {
    error_log($e->getMessage());
    exit(Errors::HEADER_ERR);
} catch (ArgumentException $e) {
    error_log($e->getMessage());
    exit(Errors::BAD_ARGUMENT);
} catch (InternalException $e) {
    error_log($e->getMessage());
    exit(Errors::INTERNAL_ERROR);
}
