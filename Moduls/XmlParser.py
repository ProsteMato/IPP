import xml.etree.ElementTree as elementTree
from .Const import Const as constants
from .Instruction import Instruction
from .Constant import Constant
from .Variable import Variable
from .TypeT import TypeT
from .Label import Label

class XmlParser:
    def __init__(self, parse_file):
        self.__parse_file = parse_file
        self.__instructions = dict()

    def parse(self):
        element = elementTree.parse(self.__parse_file)
        if not self.__check_language(element.getroot().attrib["language"]):
            raise Exception
        for element in element.getroot():
            self.__parse_instruction_element(element)
        return self.__instructions

    def __parse_instruction_element(self, element):
        args = dict()
        for arguments in element:
            if not self.__is_instruction(element.tag):
                raise Exception
            if not self.__check_op_code(element.attrib["opcode"]):
                raise Exception
            if not (func := self.__check_type(arguments.attrib["type"])):
                raise Exception
            argument = eval(func)(arguments.text, arguments.attrib["type"])
            args[arguments.tag] = argument
        self.__instructions[element.attrib["order"]] = Instruction(element.attrib["opcode"], args)


    @staticmethod
    def __is_instruction(tag):
        return tag == "instruction"

    @staticmethod
    def __check_op_code(op_code: str):
        return constants.INSTRUCTIONS.setdefault(op_code.upper(), None)

    @staticmethod
    def __check_language(language: str):
        language = language.lower()
        return language == "ippcode20"

    @staticmethod
    def __check_type(type_t):
        return constants.TYPES.setdefault(type_t, None)