<?php
    require_once "./Classes/TestClasses/FileAdministrator.php";
    require_once "./Classes/TestClasses/TestCase.php";
    require_once "./Classes/TestClasses/TestSuite.php";
    require_once "./Classes/constants.php";
    require_once "./Classes/TestClasses/HtmlGenerator.php";
    require_once "./Classes/TestClasses/ResultGenerator.php";

    $fileAdmin = new FileAdministrator(array("./Tests"), true);
    $testSuite = $fileAdmin->getTestSuites();
    $results = new ResultGenerator();
    $results->generateResults($testSuite);

