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

## parse.php
    Script or filter (parse.php in programming language PHP 7.4) loads from standard input source code
    in IPPcode20, checks for lexical and syntax error and writes on standard output XML representation of given program

    Input Arguments:
        --help          will return this text with information about script 
                        (can be used only without another argument).
                        
        --stats=file    will enable statistics and they will be stored in given file
        
        WARNING: Next arguments can be used only when --stats was specified!
        --comments      will return to given file count of comments in file
        
        --labels        will return to given file count uniq labels that are defined in program
        
        --jumps         will return to given file count of jumps, calls and returns
        
        --loc           will return to given file number of instructions without 
                        header, comments and empty lines
        NOTE: All these arguments can be used multiple times.
    
    Error codes:
        10 - using --help argument with other arguments or using arguments for 
             statistic without --stats argument.
        11 - if the given file/directory does not exist or dont have permissions to read
        12 - if the given file for write dont have req. permissions
        99 - internal error of script.
        21 - wrong or missing  header of source code written in IPPcode20.
        22 - undefined or wrong operand code in source code written in IPPcode20.
        23 - other lexical or syntax error in source code written in IPPcode 20.
## test.php
    Script (test.php written in programming language PHP 7.4) serve for automation testing successive 
    application parse.php and interpret.py. Script will pass given directory with tests and use them for automatic testing of
    correct functionality both of the previous mention applications including generated HTML5 document with results of testing
    written on STDOUT.
    
    Input Arguments:
        --help              will return this text with information about script 
                            (can be used only without another argument).
                            
        --directory=path    Test will be from this directory if this argument is not present
                            Default value is actual directory.
                            
        --recursive         With this argument test will be from given 
                            directory and all of is sub directories recursively.
                            
        --parse-script=file File with script parse.php if this argument is 
                            missing default value is in current directory.
                            
        --int-script=file   File with script interpreter.py if this argument 
                            is missing default value is in current directory.
                            
        --parse-only        Tests will be tested only on script parse.php
                            Invalid combinations with arguments int-script and int-only
                            
        --int-only          Tests will be tested only on script interpreter.py.
                            Invalid combinations with arguments parse-script and parse-only
                            
        --jexamxml=file     File with JAR package with tool A7Soft JExamXML.
                            Default Value is /pub/courses/ipp/jexamxml/jexamxml.jar
                            
        --testlist=file     File with directories with tests or test files with extension .src
                            Invalid Combination with argument directory
        --match=regexp      For choosing test basename with regexp.
        
    Error codes:
        10 - using --help argument with other arguments 
             and bad format of given arguments
        11 - if the given file/directory does not exist or dont have permissions to read
        12 - if the given file for write dont have req. permissions
        99 - internal error of script.
## interpret.py
    Program load XML representation of program and this program with utilization of standard input by parameters
    interprets and generates output. Input XML representation is generated for example with parse.php script from source
    code in language IPPcode20.
    
    Parameters:
        --help          This will print help on the output cant be combined with anything else.
        --source=file   This is source file of XML representation
        --input=file    This is input file for interpretation \
    You can omit --source or --input but not both. The omitted parameter will be loaded from standard input
    
        --stats=file    This is for enabling statistics
        --insts         This is for counting executed instructions
        --vars          This is for counting maximum initialized variables \
    You cant use --insts or --vars without --stats
    
    Return values:
        10      Bad parameter of program
        11      read permission missing, file does not exist
        12      write permission missing
        31      Bad XML representation
        32      lexical or syntax error
        52      undefined label, redefined variable
        53      bad type of instruction operand
        54      undefined variable
        55      frame does not exist
        56      missing value in frame
        57      wrong value in operand of instruction
        58      wrong operation with string



You can read more about this project and how was implemented in readme1.pdf and readme2.pdf