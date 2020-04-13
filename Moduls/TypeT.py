"""
@file: TypeT.py
@data: 13.4.2020
@author: Martin Koči <xkocim05@stud.fit.vutbr.cz>

This file is for checking Types and storing information about them.
"""

import Moduls.Const as Const
from .exceptions import InvalidXmlException


class TypeT:
    """This file is for checking Types and storing information about them."""
    def __init__(self, content, type_t):
        if content is None:
            raise InvalidXmlException("Invalid 'value' of type")
        if not Const.TYPE_REGEX.match(content):
            raise InvalidXmlException("Invalid 'type': " + content)
        self.content = content
        self.type = type_t


