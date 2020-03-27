import Moduls.Const as Const


class TypeT:

    def __init__(self, content, type_t):
        if not Const.TYPE_REGEX.match(content):
            raise Const.InvalidXmlException
        self.content = content
        self.type = type_t


