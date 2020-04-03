import Moduls.Const as Const
from .exceptions import InvalidXmlException


class TypeT:

    def __init__(self, content, type_t):
        if content is None:
            raise InvalidXmlException("Invalid 'value' of type")
        if not Const.TYPE_REGEX.match(content):
            raise InvalidXmlException("Invalid 'type': " + content)
        self.content = content
        self.type = type_t


