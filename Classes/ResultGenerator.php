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
        foreach ($testSuites as $testSuiteName => $testSuites) {
            $this->htmlGenerator->generateTestSuite
            (
                $testSuiteName, $testSuites->getTestCasesPassCount(),
                $testSuites->getTestCasesFailCount(),
                $testSuites->getTestCases()
            );
        }
        $this->htmlGenerator->endElement("body");
        $this->htmlGenerator->endElement("html");
    }
}