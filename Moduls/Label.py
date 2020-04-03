import Moduls.Const as Const
from .exceptions import InvalidXmlException


class Label:

    def __init__(self, content, type_t):
        if not Const.COMPILED_IDENTIFIER_REGEX.match(content):
            raise InvalidXmlException("Label format is not correct: " + content)
        self.content = content
        self.type = type_t
