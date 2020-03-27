from .Symbol import Symbol
import Moduls.Const as Const


class Constant(Symbol):

    def __init__(self, content, type_t):
        if not Const.CONST_REGEX.match(type_t + "@" + content):
            raise Const.InvalidXmlException
        self.content = content
        self.type = type_t
