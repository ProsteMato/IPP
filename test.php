<?php
    require_once "./Classes/FileAdministrator.php";
    require_once "./Classes/TestCase.php";
    require_once "./Classes/TestSuite.php";
    require_once "./Classes/constants.php";
    require_once "./Classes/HtmlGenerator.php";
    require_once "./Classes/ResultGenerator.php";

    $fileAdmin = new FileAdministrator(array("./Tests"), true);
    $testSuite = $fileAdmin->getTestSuites();
    $results = new ResultGenerator();
    $results->generateResults($testSuite);

