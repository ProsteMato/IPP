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
    "ADD": [Variable, Symbol, Symbol],
    "SUB": [Variable, Symbol, Symbol],
    "MUL": [Variable, Symbol, Symbol],
    "IDIV": [Variable, Symbol, Symbol],
    "LT": [Variable, Symbol, Symbol],
    "GT": [Variable, Symbol, Symbol],
    "EQ": [Variable, Symbol, Symbol],
    "AND": [Variable, Symbol, Symbol],
    "OR": [Variable, Symbol, Symbol],
    "NOT": [Variable, Symbol],
    "INT2CHAR": [Variable, Symbol],
    "STRI2INT": [Variable, Symbol, Symbol],
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
    "JUMPIFNEQ": [Label, Symbol, Symbol],
    "EXIT": [Symbol],
    "DPRINT": [Symbol],
    "BREAK": []
}

TYPES = {
    'int': 'Constant',
    'string': 'Constant',
    'bool': 'Constant',
    'label': 'Label',
    'var': 'Variable',
    'nil': 'Constant',
    'type': 'TypeT'
}

# TODO: help
HELP = """Help will be here!"""

__STRING = r'string@(([^\s\\#]|\\\d{3})+|$)'
__INT = r"int@(\+|\-)?\d+"
__BOOL = r"bool@(true|false)"
__NIL = r"nil@nil"
__SPECIAL_CHAR = r"_$&%*!?-"
IDENTIFIER = r'[A-Ža-ž' + __SPECIAL_CHAR + r'][\w' + __SPECIAL_CHAR + r']*'
TYPE_REGEX = re.compile(r'^(int|bool|string)$')
CONST_REGEX = re.compile(r"^(" + __STRING + r"|" + __INT + r"|" + __BOOL + r"|" + __NIL + r")$")
VARIABLE_REGEX = re.compile(r"^((GF|LF|TF)@" + IDENTIFIER + r")$")
COMPILED_IDENTIFIER_REGEX = re.compile(r"^(" + IDENTIFIER + r")$")

INVALID_XML_ERROR = 31
FILE_DONT_EXIST_ERROR = 11
MISSING_ARGUMENT = 10

ERROR_OK = 0

