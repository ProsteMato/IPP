"""
@file: Variable.py
@data: 13.4.2020
@author: Martin Koči <xkocim05@stud.fit.vutbr.cz>

This file is for checking Variables and storing information about them.
"""
from .Symbol import Symbol
import Moduls.Const as Const
from .exceptions import InvalidXmlException


class Variable(Symbol):
    """This class is for checking Variables and storing information about them."""
    def __init__(self, content, type_t):
        if content is None:
            raise InvalidXmlException("Invalid 'value' of type_t")
        if not Const.VARIABLE_REGEX.match(content):
            raise InvalidXmlException("Invalid form of variable: " + content)
        self.scope, self.name = content.split("@")
        self.type = type_t


