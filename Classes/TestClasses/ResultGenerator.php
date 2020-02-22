<?php


class ResultGenerator
{
    private HtmlGenerator $htmlGenerator;

    public function __construct()
    {
        $this->htmlGenerator = new HtmlGenerator();
    }

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