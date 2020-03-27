from .Symbol import Symbol
import Moduls.Const as Const


class Variable(Symbol):

    def __init__(self, content, type_t):
        if not Const.VARIABLE_REGEX.match(content):
            raise Const.InvalidXmlException
        self.scope, self.name = content.split("@")
        self.type = type_t


