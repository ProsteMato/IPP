"""
@file: Constant.py
@data: 13.4.2020
@author: Martin Koči <xkocim05@stud.fit.vutbr.cz>

This file is for checking constants and storing information about them.
"""
from .Symbol import Symbol
import Moduls.Const as Const
from Moduls.exceptions import InvalidXmlException


class Constant(Symbol):
    """This class is for checking Constants and storing information about them."""

    def __init__(self, content, type_t):
        if content is None:
            content = ""
        if type_t == "float":
            try:
                self.content = float.fromhex(content)
            except ValueError:
                raise InvalidXmlException("Invalid 'value' of constant: " + type_t + "@" + content)
        elif not Const.CONST_REGEX.match(type_t + "@" + content):
            raise InvalidXmlException("Invalid 'value' of constant: " + type_t + "@" + content)
        if type_t == "bool":
            self.content = content == "true"
        elif type_t == "int":
            self.content = int(content)
        elif type_t == "string":
            self.content = self.__parse_escape_sequence(content)
        elif type_t == "nil":
            self.content = None
        self.type = type_t

    @staticmethod
    def __parse_escape_sequence(content):
        new_content = ""
        idx = 0
        while idx < len(content):
            if content[idx] == "\\":
                new_content += chr(int(content[idx + 1: idx + 4]))
                idx += 3
            else:
                new_content += content[idx]
            idx += 1
        return new_content
