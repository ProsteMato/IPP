"""
@file: Label.py
@data: 13.4.2020
@author: Martin Koči <xkocim05@stud.fit.vutbr.cz>

This file is for checking Labels and storing information about them.
"""
import Moduls.Const as Const
from .exceptions import InvalidXmlException


class Label:
    """This class is for checking Labels and storing information about them."""
    def __init__(self, content, type_t):
        if content is None:
            raise InvalidXmlException("Invalid 'value' of label")
        if not Const.COMPILED_IDENTIFIER_REGEX.match(content):
            raise InvalidXmlException("Label format is not correct: " + content)
        self.content = content
        self.type = type_t
