<?php
    require_once "./Classes/TestClasses/FileAdministrator.php";
    require_once "./Classes/TestClasses/TestCase.php";
    require_once "./Classes/TestClasses/TestSuite.php";
    require_once "./Classes/constants.php";
    require_once "./Classes/TestClasses/HtmlGenerator.php";
    require_once "./Classes/TestClasses/ResultGenerator.php";
    require_once "./Classes/TestClasses/OptionParser.php";
    require_once "./Classes/TestClasses/Tester.php";
    require_once "./Classes/ExceptionClasses/ArgumentException.php";
    require_once "./Classes/ExceptionClasses/NotExistingFileException.php";
    require_once "./Classes/ExceptionClasses/BadArgumentCombinationException.php";
    require_once "./Classes/ExceptionClasses/RedefinitionOfArgumentException.php";
    require_once "./Classes/ExceptionClasses/RequiredValueException.php";
    require_once "./Classes/ExceptionClasses/UndefinedArgumentException.php";
    require_once "./Classes/ExceptionClasses/NotInstanceOfException.php";

    function optionsChecker(&$options) {
        if (key_exists("help", $options) && count($options) > 1) {
            throw new BadArgumentCombinationException("Help must be use alone!");
        }
        if (key_exists("parse-only", $options) &&
           (key_exists("int-only", $options) || key_exists("int-script", $options))) {
            throw new BadArgumentCombinationException("parse-only have to be use without int-only and int-script");
        }
        if (key_exists("int-only", $options) &&
            (key_exists("parse-only", $options) || key_exists("parse-script", $options))) {
            throw new BadArgumentCombinationException("int-only have to be use without parse-only and parse-script");
        }
        if (!key_exists("directory", $options)) {
            $options["directory"] = "./";
        }
        if (!key_exists("parse-script", $options)) {
            $options["parse-script"] = "./parser.php";
        }
        if (!key_exists("int-script", $options)) {
            $options["int-script"] = "./interpret.py";
        }
        if (!key_exists("jexamxml", $options)) {
            $options["jexamxml"] = "/pub/courses/ipp/jexamxml/jexamxml.jar";
        }
    }

    function printHelp() {
        exit(Errors::ERR_OK);
    }

    $longOpts = array(
        "help",
        "directory:",
        "recursive",
        "parse-script:",
        "int-script:",
        "parse-only",
        "int-only",
        "jexamxml:"
    );

    try{
        $argvParser = new OptionParser($longOpts);
        $options = $argvParser->parseArgv();
        optionsChecker($options);
        if (key_exists("help", $options)) {
            printHelp();
        }
        $fileAdmin = new FileAdministrator($options["directory"], key_exists("recursive", $options));
        $testSuite = $fileAdmin->getTestSuites();
        $tester = new Tester(
            key_exists("parse-only", $options), key_exists("int-only", $options),
            $options["parse-script"], $options["int-script"], $options["jexamxml"]
        );
        $tester->runTests($testSuite);
        $results = new ResultGenerator();
        $results->generateResults($testSuite);
    } catch (NotExistingFileException $e){
        error_log($e->getMessage());
        exit(Errors::NON_EXISTING_FILE);
    } catch (ArgumentException $e) {
        error_log($e->getMessage());
        exit(Errors::BAD_ARGUMENT);
    } catch (NotInstanceOfException $e) {
        error_log($e->getMessage());
        exit(Errors::INTERNAL_ERROR);
    }


