import xml.etree.ElementTree as elementTree
import Moduls.Const as Const
from .Instruction import Instruction
from .Constant import Constant
from .Variable import Variable
from .TypeT import TypeT
from .Label import Label
import re


class XmlParser:
    def __init__(self, parse_file, program):
        self.__parse_file = parse_file
        self.__program = program
        self.__instructions = {}

    def parse(self):
        element = elementTree.parse(self.__parse_file)
        self.__check_program_attrib(element.getroot().attrib)
        if not self.__check_language(element.getroot().attrib["language"]):
            raise Const.InvalidXmlException
        for element in element.getroot():
            self.__check_instruction_attrib(element.attrib)
            self.__parse_instruction_element(element)
        return {k: self.__instructions[k] for k in sorted(self.__instructions)}

    def __parse_instruction_element(self, element):
        args = dict()
        for argument in element:
            self.__check_argument(argument)
            if not self.__is_instruction(element.tag):
                raise Const.InvalidXmlException
            if not self.__check_op_code(element.attrib["opcode"]):
                raise Const.InvalidXmlException
            if not (func := self.__check_type(argument.attrib["type"])):
                raise Const.InvalidXmlException
            args[argument.tag] = eval(func)(argument.text, argument.attrib["type"])
        self.__is_order_int(element.attrib["order"])
        self.__instructions[int(element.attrib["order"])] = Instruction(self.__program, element.attrib["opcode"].upper(), args)

    @staticmethod
    def __is_instruction(tag):
        return tag == "instruction"

    @staticmethod
    def __is_order_int(order):
        try:
            int(order)
        except ValueError:
            raise Const.InvalidXmlException

    @staticmethod
    def __check_program_attrib(element):
        if "language" not in element:
            raise Const.InvalidXmlException

    @staticmethod
    def __check_instruction_attrib(instruction):
        if "order" not in instruction or "opcode" not in instruction:
            raise Const.InvalidXmlException

    @staticmethod
    def __check_argument(argument):
        if "type" not in argument.attrib:
            raise Const.InvalidXmlException
        if re.match("^(arg1|arg2|arg3)$", argument.tag) is None:
            raise Const.InvalidXmlException

    @staticmethod
    def __check_op_code(op_code: str):
        return Const.INSTRUCTIONS.setdefault(op_code.upper(), None)

    @staticmethod
    def __check_language(language: str):
        return language.lower() == "ippcode20"

    @staticmethod
    def __check_type(type_t):
        return Const.TYPES.setdefault(type_t, None)


