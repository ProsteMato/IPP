<?php
/**
 * @file    test.php
 * @date    1.3.2020
 * @author  Martin Koči (xkocim05@stud.fit.vutbr.cz)
 * @brief   This script is testing input testing files and generating results to HTML document.
 */

require_once "./Classes/TestClasses/FileAdministrator.php";
require_once "./Classes/TestClasses/TestCase.php";
require_once "./Classes/TestClasses/TestSuite.php";
require_once "./Classes/constants.php";
require_once "./Classes/TestClasses/HtmlGenerator.php";
require_once "./Classes/TestClasses/ResultGenerator.php";
require_once "./Classes/TestClasses/Tester.php";
require_once "./Classes/ExceptionClasses/InternalException.php";
require_once "./Classes/ExceptionClasses/ArgumentException.php";
require_once "./Classes/ExceptionClasses/NotExistingFileException.php";
require_once "./Classes/ExceptionClasses/BadArgumentCombinationException.php";
require_once "./Classes/ExceptionClasses/RedefinitionOfArgumentException.php";
require_once "./Classes/ExceptionClasses/RequiredValueException.php";
require_once "./Classes/ExceptionClasses/UndefinedArgumentException.php";
require_once "./Classes/ExceptionClasses/NotInstanceOfException.php";
require_once "./Classes/TestClasses/ArgParser.php";


function printHelp() {
    echo TEST_HELP;
    exit(Errors::ERR_OK);
}

try {
    $argParser = (new ArgParser())
        ->addArgument("parse-only", false, false, array("int-only", "int-script"))
        ->addArgument("int-only", false, false, array("parse-only", "parse-script"))
        ->addArgument("jexamxml", true, "/pub/courses/ipp/jexamxml/jexamxml.jar")
        ->addArgument("parse-script", true, "./parser.php")
        ->addArgument("int-script", true, "./interpret.py")
        ->addArgument("directory", true, "./", array("testlist"))
        ->addArgument("recursive", false, false)
        ->addArgument("testlist", true, null, array("directory"))
        ->addArgument("match", true, "//");

    $options = $argParser->parseArguments();
    if (isset($options["help"]))
        printHelp();
    $files = (key_exists("testlist", $options)) ? $options["testlist"] : $options["directory"];
    $fileAdmin = new FileAdministrator($files, $options["recursive"], $options["match"]);
    $testSuite = $fileAdmin->getTestSuites();
    $tester = new Tester(
        $options["parse-only"], $options["int-only"], $options["parse-script"], $options["int-script"], $options["jexamxml"]
    );
    $tester->runTests($testSuite);
    $results = new ResultGenerator();
    $results->generateResults($testSuite);
} catch (NotExistingFileException $e) {
    error_log($e->getMessage());
    exit(Errors::NON_EXISTING_FILE);
} catch (ArgumentException $e) {
    error_log($e->getMessage());
    exit(Errors::BAD_ARGUMENT);
} catch (InternalException $e) {
    error_log($e->getMessage());
    exit(Errors::INTERNAL_ERROR);
}


