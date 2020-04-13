"""
@file: exceptions.py
@data: 13.4.2020
@author: Martin Koči <xkocim05@stud.fit.vutbr.cz>

This file is for creating additional specific type.
"""


class InvalidXmlException(Exception):
    """This Exception is for lexical or syntax error"""
    def __init__(self, msg):
        self.msg = msg
        self.error = 32


class WritePermissionError(PermissionError):
    """When file does not have right for writing"""
    def __init__(self, msg):
        self.msg = msg
        self.error = 12


class InvalidCodeException(Exception):
    """This is when is undefined label or redefined variable"""
    def __init__(self, msg):
        self.msg = msg
        self.error = 52


class BadOperandTypeException(Exception):
    """Bad type of argument of instruction"""
    def __init__(self, msg):
        self.msg = msg
        self.error = 53


class NonExistingVarException(Exception):
    """Undefined variable"""
    def __init__(self, msg):
        self.msg = msg
        self.error = 54


class InvalidFrameException(Exception):
    """Frame does not exist"""
    def __init__(self, msg):
        self.msg = msg
        self.error = 55


class MissingValueException(Exception):
    """Frame is empty"""
    def __init__(self, msg):
        self.msg = msg
        self.error = 56


class BadValueException(Exception):
    """ Bad value of argument """
    def __init__(self, msg):
        self.msg = msg
        self.error = 57


class InvalidStringOperationException(Exception):
    """ Bad operation with string """
    def __init__(self, msg):
        self.msg = msg
        self.error = 58
