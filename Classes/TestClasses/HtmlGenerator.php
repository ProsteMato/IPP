<?php


class HtmlGenerator
{

    private int $id;

    public function __construct()
    {
        $this->id = 0;
    }

    function createHeader()
    {
        ?>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet">
            <title>Tests Results</title>
            <style>
                * {
                    font-family: 'Open Sans', sans-serif;
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
                    font-size: 14px;
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
                    font-size: 12px;
                    font-weight: 700;
                    text-align: left;
                }

                .table_cell {
                    color: #383a42;
                    font-size: 12px;
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
            </style>
        </head>
        <?php
    }
    public function createHtmlElement()
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <?php
    }

    public function createElement($tag)
    {
        echo "<$tag>\n";
    }

    public function endElement($tag)
    {
        echo "</$tag>\n";
    }

    public function generateTestSuite(string $testSuiteName,int $testPassed,int $testFailed,array $testCases)
    {
        $this->id++;
        $total = $testFailed + $testPassed;
        ?>
        <div class="collapse">
        <input type="checkbox" class="collapse_input" id="collapse<?php echo "$this->id"?>" />
        <label for="collapse<?php echo "$this->id"?>" class="collapse_label">
            <span class="collapse_parameter"><?php echo $testSuiteName ?></span>
            <span class="collapse_parameter collapse_parameter_passed">Passed: <strong><?php echo $testPassed ?></strong></span>
            <span class="collapse_parameter collapse_parameter_failed">Failed: <strong><?php echo $testFailed ?></strong></span>
            <span class="collapse_parameter"><strong>Total: <?php echo $total ?></strong></span>
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
            ?>
                <tr>
                    <td class="table_cell table_cell_name"><?php echo basename($testCaseName) ?></td>
                    <td class="table_cell <?php echo $resultClass ?>"><?php echo ($testCase->hasPassed())? "Passed" : "Failed" ?></td>
                </tr>
            <?php
        }
    }
}