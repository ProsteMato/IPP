<?php
    require_once "./Classes/FileAdministrator.php";
    require_once "./Classes/TestCase.php";
    require_once "./Classes/TestSuite.php";

    $fileAdmin = new FileAdministrator(array("./Tests"), true);
    $fileAdmin->getTestSuites();