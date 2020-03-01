<?php

/**
 * @class   ResultGenerator
 * @file    ResultGenerator.php
 * @date    1.3.2020
 * @author  Martin Koči (xkocim05@stud.fit.vutbr.cz)
 * @brief   This class is generating results for Testing
 */
class ResultGenerator
{
    private HtmlGenerator $htmlGenerator;

    public function __construct()
    {
        $this->htmlGenerator = new HtmlGenerator();
    }

    /**
     * @param array $testSuites TestSuites
     * @brief This method is generating results to HTML document.
     */
    public function generateResults(array $testSuites) {
        $this->htmlGenerator->createHtmlElement();
        $this->htmlGenerator->createHeader();
        $this->htmlGenerator->createElement("body");
        foreach ($testSuites as $testSuiteName => $testSuite) {
            $this->htmlGenerator->generateTestSuite
            (
                $testSuiteName, $testSuite->getTestCasesPassCount(),
                $testSuite->getTestCasesFailCount(),
                $testSuite->getTestCases()
            );
        }
        $this->htmlGenerator->endElement("body");
        $this->htmlGenerator->endElement("html");
    }
}