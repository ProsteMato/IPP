

class Instruction:

    def __init__(self, name, arguments):
        self.__name = name
        self.__arguments = arguments

    def get_name(self):
        return self.__name

    def get_arguments(self):
        return self.__arguments
