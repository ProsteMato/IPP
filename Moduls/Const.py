import re


class Const:

    INSTRUCTIONS = {
        "MOVE": ['var', 'symb'],
        "CREATEFRAME": [],
        "PUSHFRAME": [],
        "POPFRAME": [],
        "DEFVAR": ['var'],
        "CALL": ['label'],
        "RETURN": [],
        "PUSHS": ['symb'],
        "POPS": ['var'],
        "ADD": ['var', 'symb', 'symb'],
        "SUB": ['var', 'symb', 'symb'],
        "MUL": ['var', 'symb', 'symb'],
        "IDIV": ['var', 'symb', 'symb'],
        "LT": ['var', 'symb', 'symb'],
        "GT": ['var', 'symb', 'symb'],
        "EQ": ['var', 'symb', 'symb'],
        "AND": ['var', 'symb', 'symb'],
        "OR": ['var', 'symb', 'symb'],
        "NOT": ['var', 'symb'],
        "INT2CHAR": ['var', 'symb'],
        "STRI2INT": ['var', 'symb', 'symb'],
        "READ": ['var', 'type'],
        "WRITE": ['symb'],
        "CONCAT": ['var', 'symb', 'symb'],
        "STRLEN": ['var', 'symb'],
        "GETCHAR": ['var', 'symb', 'symb'],
        "SETCHAR": ['var', 'symb', 'symb'],
        "TYPE": ['var', 'symb'],
        "LABEL": ['label'],
        "JUMP": ['label'],
        "JUMPIFEQ": ['label', 'symb', 'symb'],
        "JUMPIFNEQ": ['label', 'symb', 'symb'],
        "EXIT": ['symb'],
        "DPRINT": ['symb'],
        "BREAK": []
    }

    TYPES = {
        'int': 'Constant',
        'float': 'Constant',
        'string': 'Constant',
        'bool': 'Constant',
        'label': 'Label',
        'var': 'Variable',
        'nil': 'Constant',
        'type': 'TypeT'
    }

    __STRING = r"string@(([^\\#]|\d{3})+|$)"
    __INT = r"int@(\+|\-)?\d+"
    __BOOL = r"bool@(true|false)"
    __NIL = r"nil@nil"
    __SPECIAL_CHAR = r"_$&%*!?-"
    IDENTIFIER = r'[A-Za-z' + __SPECIAL_CHAR + r'][\w' + __SPECIAL_CHAR + r']*'
    TYPE_REGEX = re.compile(r'^(int|bool|string)$')
    CONST_REGEX = re.compile(r"^(" + __STRING + r"|" + __INT + r"|" + __BOOL + r"|" + __NIL + r")$")
    VARIABLE_REGEX = re.compile(r"^((GF|LF|TF)@" + IDENTIFIER + r")$")
    COMPILED_IDENTIFIER_REGEX = re.compile(r"^(" + IDENTIFIER + r")$")



