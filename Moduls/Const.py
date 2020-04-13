"""
@file: Const.py
@data: 13.4.2020
@author: Martin Koči <xkocim05@stud.fit.vutbr.cz>

This file is for storing constants
"""
import re
from .Symbol import Symbol
from .Variable import Variable
from .TypeT import TypeT
from .Label import Label

INSTRUCTIONS = {
    "MOVE": [Variable, Symbol],
    "CREATEFRAME": [],
    "PUSHFRAME": [],
    "POPFRAME": [],
    "DEFVAR": [Variable],
    "CALL": [Label],
    "RETURN": [],
    "PUSHS": [Symbol],
    "POPS": [Variable],
    "CLEARS": [],
    "ADD": [Variable, Symbol, Symbol],
    "ADDS": [],
    "SUB": [Variable, Symbol, Symbol],
    "SUBS": [],
    "MUL": [Variable, Symbol, Symbol],
    "MULS": [],
    "IDIV": [Variable, Symbol, Symbol],
    "IDIVS": [],
    "DIV": [Variable, Symbol, Symbol],
    "DIVS": [],
    "LT": [Variable, Symbol, Symbol],
    "LTS": [],
    "GT": [Variable, Symbol, Symbol],
    "GTS": [],
    "EQ": [Variable, Symbol, Symbol],
    "EQS": [],
    "AND": [Variable, Symbol, Symbol],
    "ANDS": [],
    "OR": [Variable, Symbol, Symbol],
    "ORS": [],
    "NOT": [Variable, Symbol],
    "NOTS": [],
    "FLOAT2INT": [Variable, Symbol],
    "FLOAT2INTS": [],
    "INT2FLOAT": [Variable, Symbol],
    "INT2FLOATS": [],
    "INT2CHAR": [Variable, Symbol],
    "INT2CHARS": [],
    "STRI2INT": [Variable, Symbol, Symbol],
    "STRI2INTS": [],
    "READ": [Variable, TypeT],
    "WRITE": [Symbol],
    "CONCAT": [Variable, Symbol, Symbol],
    "STRLEN": [Variable, Symbol],
    "GETCHAR": [Variable, Symbol, Symbol],
    "SETCHAR": [Variable, Symbol, Symbol],
    "TYPE": [Variable, Symbol],
    "LABEL": [Label],
    "JUMP": [Label],
    "JUMPIFEQ": [Label, Symbol, Symbol],
    "JUMPIFEQS": [Label],
    "JUMPIFNEQ": [Label, Symbol, Symbol],
    "JUMPIFNEQS": [Label],
    "EXIT": [Symbol],
    "DPRINT": [Symbol],
    "BREAK": []
}

TYPES = {
    'int': 'Constant',
    'string': 'Constant',
    'bool': 'Constant',
    'float': 'Constant',
    'label': 'Label',
    'var': 'Variable',
    'nil': 'Constant',
    'type': 'TypeT'
}

# TODO: help
HELP = """Program load XML representation of program and this program with utilization of standard input by parameters
interprets and generates output. Input XML representation is generated for example with parse.php script from source
code in language IPPcode20.

Parameters:
--help          This will print help on the output cant be combined with anything else.
--source=file   This is source file of XML representation
--input=file    This is input file for interpretation
You can omit --source or --input but not both. The omitted parameter will be loaded from standard input

--stats=file    This is for enabling statistics
--insts         This is for counting executed instructions
--vars          This is for counting maximum initialized variables
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

"""

__STRING = r'string@(([^\s\\#]|\\\d{3})+|$)'
__INT = r"int@(\+|\-)?\d+"
__BOOL = r"bool@(true|false)"
__NIL = r"nil@nil"
__SPECIAL_CHAR = r"_$&%*!?-"
IDENTIFIER = r'[A-Ža-ž' + __SPECIAL_CHAR + r'][\w' + __SPECIAL_CHAR + r']*'
TYPE_REGEX = re.compile(r'^(int|bool|string|float)$')
CONST_REGEX = re.compile(r"^(" + __STRING + r"|" + __INT + r"|" + __BOOL + r"|" + __NIL + r")$")
VARIABLE_REGEX = re.compile(r"^((GF|LF|TF)@" + IDENTIFIER + r")$")
COMPILED_IDENTIFIER_REGEX = re.compile(r"^(" + IDENTIFIER + r")$")

INVALID_XML_ERROR = 31
FILE_DONT_EXIST_ERROR = 11
MISSING_ARGUMENT = 10

ERROR_OK = 0

