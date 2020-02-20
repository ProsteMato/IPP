<?php



class Instructions
{
    const INSTRUCTIONS = array(
        "MOVE" => [Types::VARIABLE, Types::SYMBOL],
        "CREATEFRAME" => [],
        "PUSHFRAME" => [],
        "POPFRAME" => [],
        "DEFVAR" => [Types::VARIABLE],
        "CALL" => [Types::LABEL],
        "RETURN" => [],
        "PUSHS" => [Types::SYMBOL],
        "POPS" => [Types::VARIABLE],
        "ADD" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
        "SUB" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
        "MUL" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
        "IDIV" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
        "LT" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
        "GT" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
        "EQ" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
        "AND" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
        "OR" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
        "NOT" => [Types::VARIABLE, Types::SYMBOL],
        "INT2CHAR" => [Types::VARIABLE, Types::SYMBOL],
        "STR2INT" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
        "READ" => [Types::VARIABLE, Types::TYPE],
        "WRITE" => [Types::SYMBOL],
        "CONCAT" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
        "STRLEN" => [Types::VARIABLE, Types::SYMBOL],
        "GETCHAR" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
        "SETCHAR" => [Types::VARIABLE, Types::SYMBOL, Types::SYMBOL],
        "TYPE" => [Types::VARIABLE, Types::SYMBOL],
        "LABEL" => [Types::LABEL],
        "JUMP" => [Types::LABEL],
        "JUMPIFEQ" => [Types::LABEL, Types::SYMBOL, Types::SYMBOL],
        "JUMPIFNEQ" => [Types::LABEL, Types::SYMBOL, Types::SYMBOL],
        "EXIT" => [Types::SYMBOL],
        "DPRINT" => [Types::SYMBOL],
        "BREAK" => []
    );

}

class Errors
{
    const BAD_ARGUMENT = 10;
    const HEADER_ERR = 21;
    const INSTRUCTION_ERR = 22;
    const LEX_OR_SYNTAX_ERR = 23;
    const ERR_OK = 0;
    const NON_EXISTING_FILE = 11;
}

class Types
{
    const VARIABLE = 1;
    const LABEL = 2;
    const SYMBOL = 3;
    const TYPE = 4;
}

class Regex
{
    private const STRING = "string@(([^\\\#]|\\\\\d{3})+|$)";
    private const INT = "int@(\+|\-)?\d+";
    private const BOOL = "bool@(true|false)";
    private const NIL = "nil@nil";
    private const CONST = self::STRING . "|" . self::INT . "|" . self::BOOL . "|" . self::NIL;
    private const SPECIAL_CHAR = "_$&%*!?-";
    private const IDENTIFIER = "[[:alpha:]" . self::SPECIAL_CHAR . "][[:alnum:]" . self::SPECIAL_CHAR . "]*";
    const COMMENT = "(#[^\n]*)";
    const DELIMITER = "[\s\t]";
    const TYPE = "(int|bool|string)";
    const VARIABLE = "(GF|LF|TF)@" . self::IDENTIFIER;
    const LABEL = self::IDENTIFIER;
    const SYMBOL = "(". self::CONST . "|" . self::VARIABLE . ")";
}

const HELP = 'Script or filter (parser.php in programming language PHP 7.4) loads from standard input source code
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
    21 - wrong or missing  header of source code written in IPPcode20.
    22 - undefined or wrong operand code in source code written in IPPcode20.
    23 - other lexical or syntax error in source code written in IPPcode 20.'.PHP_EOL;
