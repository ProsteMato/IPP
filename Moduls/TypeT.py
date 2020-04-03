import Moduls.Const as Const
from .exceptions import InvalidXmlException


class TypeT:

    def __init__(self, content, type_t):
        if not Const.TYPE_REGEX.match(content):
            raise InvalidXmlException("Invalid 'type': " + content)
        self.content = content
        self.type = type_t


