<?php
    require_once "./Classes/TestClasses/FileAdministrator.php";
    require_once "./Classes/TestClasses/TestCase.php";
    require_once "./Classes/TestClasses/TestSuite.php";
    require_once "./Classes/constants.php";
    require_once "./Classes/TestClasses/HtmlGenerator.php";
    require_once "./Classes/TestClasses/ResultGenerator.php";
    require_once "./Classes/TestClasses/Tester.php";
    require_once "./Classes/ExceptionClasses/ArgumentException.php";
    require_once "./Classes/ExceptionClasses/NotExistingFileException.php";
    require_once "./Classes/ExceptionClasses/BadArgumentCombinationException.php";
    require_once "./Classes/ExceptionClasses/RedefinitionOfArgumentException.php";
    require_once "./Classes/ExceptionClasses/RequiredValueException.php";
    require_once "./Classes/ExceptionClasses/UndefinedArgumentException.php";
    require_once "./Classes/ExceptionClasses/NotInstanceOfException.php";
    require_once "./Classes/TestClasses/ArgParser.php";


    function printHelp() {
        exit(Errors::ERR_OK);
    }

    try {
        $argParser = (new ArgParser())
            ->addArgument("parse-only", false, false, array("int-only", "int-script"))
            ->addArgument("int-only", false, false, array("parse-only", "parse-script"))
            ->addArgument("jexamxml", true, "/pub/courses/ipp/jexamxml/jexamxml.jar")
            ->addArgument("parse-script", true, "./parser.php")
            ->addArgument("int-script", true, "./interpret.py")
            ->addArgument("directory", true, "./")
            ->addArgument("recursive", false, false);
        $options = $argParser->parseArguments();
        $fileAdmin = new FileAdministrator($options["directory"], $options["recursive"]);
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
    } catch (NotInstanceOfException $e) {
        error_log($e->getMessage());
        exit(Errors::INTERNAL_ERROR);
    }


