<?php

/**
 * @class   HtmlGenerator
 * @file    HtmlGenerator.php
 * @date    1.3.2020
 * @author  Martin Koči (xkocim05@stud.fit.vutbr.cz)
 * @brief   This class is for generating HTML document for results of testing
 */
class HtmlGenerator
{

    private int $id;

    public function __construct()
    {
        $this->id = 0;
    }

    /**
     * @brief This method will generate header of HTML document and styles that will be used.
     */
    public function createHeader()
    {
        ?>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Tests Results</title>
            <style>
                * {
                    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
                }

                .collapse_input {
                    display: none;
                }

                .collapse_label {
                    position: relative;
                    display: flex;
                    background-color: #f8f9fc;
                    border-bottom: 1px solid #e7e9f3;
                    color: #383a42;
                    font-size: 18px;
                    line-height: 1.5;
                    transition: background-color 0.25s;
                    cursor: pointer;
                }

                .collapse_label::after {
                    position: absolute;
                    top: 50%;
                    right: 10px;
                    transform: translateY(-50%);
                    content: '';
                    height: 20px;
                    width: 20px;
                    background: center / contain no-repeat url("data:image/svg+xml;charset=UTF-8, %3csvg xmlns='http://www.w3.org/2000/svg' height='24' viewBox='0 0 24 24' width='24'%3e%3cpath d='M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z' fill='%23383a42' /%3e%3cpath d='M0 0h24v24H0V0z' fill='none'/%3e%3c/svg%3e");
                    transition: transform 0.25s;
                }

                .collapse_input:checked + .collapse_label::after {
                    transform: rotate(+180deg) translateY(50%);
                }

                .collapse_label:hover {
                    background-color: #f1f3f9;
                }

                .collapse_content {
                    max-height: 0;
                    padding: 10px 15px;
                    opacity: 0;
                    overflow: hidden;
                    background-color: #f8f9fc;
                    color: #383a42;
                    font-size: 14px;
                    line-height: 1.5;
                    transition: opacity 0.25s, max-height 0.25s;
                }

                .collapse_parameter {
                    padding: 10px 20px;
                }

                .collapse_parameter_passed {
                    color: #1eb906;
                }

                .collapse_parameter_failed {
                    color: #d60f0f;
                }

                .collapse_input:checked ~ .collapse_content {
                    max-height: 100%;
                    opacity: 1;
                }

                .table {
                    border-collapse: collapse;
                }

                .table_head_cell {
                    padding: 5px 10px;
                    color: #8e919f;
                    font-size: 16px;
                    font-weight: 700;
                    text-align: left;
                }

                .table_cell {
                    color: #383a42;
                    font-size: 15px;
                    padding: 5px 10px;
                }

                .table_cell_passed,
                .table_cell_failed {
                    position: relative;
                    padding-right: 36px;
                    box-sizing: border-box;
                    width: 50px;
                }

                .table_cell_passed {
                    color: #1eb906;
                }

                .table_cell_failed {
                    color: #d60f0f;
                }

                .table_cell_passed::after,
                .table_cell_failed::after {
                    position: absolute;
                    top: 50%;
                    right: 10px;
                    transform: translateY(-50%);
                    height: 16px;
                    width: 16px;
                    content: '';
                    background: center / contain no-repeat;
                }

                .table_cell_passed::after {
                    background-image: url("data:image/svg+xml;charset=UTF-8, %3csvg xmlns='http://www.w3.org/2000/svg' height='24' viewBox='0 0 24 24' width='24'%3e%3cpath d='M0 0h24v24H0z' fill='none'/%3e%3cpath d='M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z' fill='%231eb906' /%3e%3c/svg%3e");
                }

                .table_cell_failed::after {
                    background-image: url("data:image/svg+xml;charset=UTF-8, %3csvg xmlns='http://www.w3.org/2000/svg' height='24' viewBox='0 0 24 24' width='24'%3e%3cpath d='M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z' fill='%23d60f0f'/%3e%3cpath d='M0 0h24v24H0z' fill='none'/%3e%3c/svg%3e");
                }

                .header {
                    position: relative;
                    display: flex;
                    background-color: #f8f9fc;
                    border-bottom: 1px solid #e7e9f3;
                    color: #383a42;
                    font-size: 18px;
                    line-height: 1.5;
                    transition: background-color 0.25s;
                }
            </style>
        </head>
        <?php
    }

    /**
     * @brief This method will generate starting HTML document
     */
    public function createHtmlElement()
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <?php
    }

    /**
     * @param string $tag   Tag that will be generated.
     * @brief This method will generate starting tag
     */
    public function createElement($tag)
    {
        echo "<$tag>\n";
    }

    /**
     * @param string $tag Tag will be generated
     * @brief This method will generate ending tag
     */
    public function endElement($tag)
    {
        echo "</$tag>\n";
    }

    public function generateResultHeader(int $testSuites, int $tests, int $passedTests, int $failedTests)
    {
    ?>
        <div class="header">
            <table>
                <thead></thead>
                <tbody>
                    <tr>
                        <td class="collapse_parameter">Total Number of Test Suites:  <strong><?php echo "$testSuites" ?></strong></td>
                    </tr>
                    <tr>
                        <td class="collapse_parameter">Total Number of Test Cases:  <strong><?php echo "$tests" ?></strong></td>
                    </tr>
                    <tr>
                        <td class="collapse_parameter collapse_parameter_passed">Total Passed:  <strong><?php echo "$passedTests" ?></strong></td>
                    </tr>
                    <tr>
                        <td class="collapse_parameter collapse_parameter_failed">Total Failed:  <strong><?php echo "$failedTests" ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php
    }

    /**
     * @param string $testSuiteName Test suite name
     * @param int $testPassed   count of passed tests
     * @param int $testFailed   count of failed tests
     * @param array $testCases  array of test cases.
     * @brief This method will generate results for each TestSuite.
     */
    public function generateTestSuite(string $testSuiteName,int $testPassed,int $testFailed,array $testCases)
    {
        $this->id++;
        $total = $testFailed + $testPassed;
        ?>
        <div class="collapse">
        <input type="checkbox" class="collapse_input" id="collapse<?php echo "$this->id"?>" />
        <label for="collapse<?php echo "$this->id"?>" class="collapse_label">
            <span class="collapse_parameter"><?php echo "$testSuiteName" ?></span>
            <span class="collapse_parameter collapse_parameter_passed">Passed: <strong><?php echo "$testPassed" ?></strong></span>
            <span class="collapse_parameter collapse_parameter_failed">Failed: <strong><?php echo "$testFailed" ?></strong></span>
            <span class="collapse_parameter"><strong>Total: <?php echo "$total" ?></strong></span>
        </label>
        <div class="collapse_content">
            <table class="table">
                <thead>
                    <tr>
                        <th class="table_head_cell">Test cases</th>
                        <th class="table_head_cell">Results</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $this->generateTestCases($testCases);?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    }

    private function generateTestCases($testCases)
    {
        foreach($testCases as $testCaseName => $testCase)
        {
            $resultClass = ($testCase->hasPassed()) ? "table_cell_passed" : "table_cell_failed"
            ?><tr>
                <td class="table_cell table_cell_name"><?php echo basename($testCaseName) ?></td>
                <td class="table_cell <?php echo $resultClass ?>"><?php echo ($testCase->hasPassed())? "Passed" : "Failed" ?></td>
            </tr>
            <?php
        }
    }
}