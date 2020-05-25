# IPP
Author: Martin Koči (xkocim05)

This project is for class Principles of Programming Languages.
Main goals of this project:
- create a parser that will convert IPPcode20 language to XML
- create a interpret for out custom programing language
- create test script for testing parsing and interpretation

## Examples of conversion
file in IPPcode20 language
```
.IPPcode20
WRITE int@0
WRITE string@ábč
```
converted file in xml format
```xml
<?xml version="1.0" encoding="UTF-8"?>
<program language="IPPcode20">
    <instruction order="1" opcode="WRITE">
        <arg1 type="int">0</arg1>
    </instruction>
    <instruction order="2" opcode="WRITE">
        <arg1 type="string">ábč</arg1>
    </instruction>
</program>
```

## Output of test script
![](images/tests.gif)

You can read more about this project and how was implemented in readme1.pdf and readme2.pdf