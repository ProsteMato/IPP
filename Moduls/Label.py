import Moduls.Const as Const


class Label:

    def __init__(self, content, type_t):
        if not Const.COMPILED_IDENTIFIER_REGEX.match(content):
            raise Const.InvalidXmlException
        self.content = content
        self.type = type_t
