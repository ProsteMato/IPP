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
HELP = """Help will be here!"""

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

