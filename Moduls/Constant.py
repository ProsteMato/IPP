from .Symbol import Symbol
import Moduls.Const as Const


class Constant(Symbol):

    def __init__(self, content, type_t):
        if not Const.CONST_REGEX.match(type_t + "@" + content):
            raise Const.InvalidXmlException
        if type_t == "bool":
            self.content = True if content == "true" else "false"
        elif type_t == "int":
            self.content = int(content)
        elif type_t == "string":
            self.content = self.__parse_escape_sequence(content)
        elif type_t == "nil":
            self.content = None
        self.type = type_t

    def __parse_escape_sequence(self, content):
        new_content = ""
        idx = 0
        while idx < len(content):
            if content[idx] == "\\":
                new_content += chr(int(content[idx + 1: idx + 4]))
                idx += 4
            else:
                new_content += content[idx]
            idx += 1
        return new_content
