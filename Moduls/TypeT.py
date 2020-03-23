from .Argument import Argument
from .Const import Const


class TypeT(Argument):

    def __init__(self, content, type_t):
        if not Const.TYPE_REGEX.match(content):
            raise Exception
        self.__content = content
        self.__type_t = type_t

    def get_content(self):
        return self.__content

    def get_type(self):
        return self.__type_t


