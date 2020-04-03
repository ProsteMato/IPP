import xml.etree.ElementTree as elementTree
import Moduls.Const as Const
from .Instruction import Instruction
from Moduls.exceptions import InvalidXmlException
from .Constant import Constant
from .Variable import Variable
from .Label import Label
from .TypeT import TypeT
import re
import sys


class XmlParser:
    def __init__(self, parse_file, program):
        self.__parse_file = parse_file
        self.__program = program
        self.__instructions = []

    def parse(self):
        element = elementTree.parse(self.__parse_file).getroot()
        self.__check_program_element(element)
        for element in element:
            self.__check_instruction_element(element)
            self.__parse_instruction_element(element)
        return sorted(self.__instructions, key=lambda x: x.order)

    def __parse_instruction_element(self, element):
        args = dict()
        for argument in element:
            self.__check_argument_element(argument)
            func = self.__get_and_check_type(argument.attrib["type"])
            args[argument.tag] = eval(func)(argument.text, argument.attrib["type"])
        self.__instructions.append(Instruction(self.__program, int(element.attrib["order"]), element.attrib["opcode"].upper(), args))

    def __check_program_element(self, element):
        self.__is_program(element)
        self.__check_text_outside_of_elements(element.tail)
        self.__check_text_outside_of_elements(element.text)
        self.__check_program_allowed_attrib(element)
        self.__check_program_attrib(element.attrib)
        self.__check_language(element.attrib["language"])

    def __check_instruction_element(self, element):
        self.__is_instruction(element.tag)
        self.__check_text_outside_of_elements(element.text)
        self.__check_text_outside_of_elements(element.tail)
        self.__check_instruction_allowed_attrib(element)
        self.__check_instruction_attrib(element.attrib)
        self.__check_op_code(element.attrib["opcode"])
        self.__is_correct_order(element.attrib["order"])

    def __check_argument_element(self, argument):
        self.__is_argument(argument)
        self.__check_argument_allowed_attrib(argument)
        self.__check_argument_attrib(argument)
        self.__check_text_outside_of_elements(argument.tail)

    @staticmethod
    def __is_program(element):
        if element.tag != "program":
            raise InvalidXmlException(element.tag + " is not correct root element!")

    @staticmethod
    def __is_instruction(tag):
        if tag != "instruction":
            raise InvalidXmlException(tag + " is not correct instruction element!")

    @staticmethod
    def __is_argument(argument):
        if re.match(r"^(arg1|arg2|arg3)$", argument.tag) is None:
            raise InvalidXmlException(argument.tag + " is unsupported name of element in instruction!")

    @staticmethod
    def __check_program_allowed_attrib(element):
        if not all(attribute in ["language", "name", "description"] for attribute in element.keys()):
            raise InvalidXmlException("One or more unsupported attributes in element program!")

    @staticmethod
    def __check_instruction_allowed_attrib(element):
        if not all(attribute in ["order", "opcode"] for attribute in element.keys()):
            raise InvalidXmlException("One or more unsupported attributes in element instruction!")

    @staticmethod
    def __check_argument_allowed_attrib(element):
        if not all(attribute in ["type"] for attribute in element.keys()):
            raise InvalidXmlException("One or more unsupported attributes in element argument!")

    @staticmethod
    def __check_program_attrib(element):
        if "language" not in element:
            raise InvalidXmlException("language attribute is required in program element!")

    @staticmethod
    def __check_instruction_attrib(instruction):
        if "order" not in instruction or "opcode" not in instruction:
            raise InvalidXmlException("order and opCode attributes are required in instruction element!")

    @staticmethod
    def __check_argument_attrib(argument):
        if "type" not in argument.attrib:
            raise InvalidXmlException("type attribute is required in argument element!")

    @staticmethod
    def __check_text_outside_of_elements(element):
        if element is not None:
            element_text = re.sub(r'(\r|\n|\t|\s)', '', str(element))
            if len(element_text) > 0:
                raise InvalidXmlException("Text outside of elements is not allowed!")

    def __is_correct_order(self, order):
        try:
            order = int(order)
        except ValueError:
            raise InvalidXmlException("Order is not a number!")
        if order <= 0:
            raise InvalidXmlException("Order must be positive number!")
        for instruction in self.__instructions:
            if order == instruction.order:
                raise InvalidXmlException("Order is duplicate of some other element order!")

    @staticmethod
    def __check_op_code(op_code: str):
        if op_code.upper() not in Const.INSTRUCTIONS:
            raise InvalidXmlException("Unsupported OpCode: " + op_code + "!")

    @staticmethod
    def __check_language(language: str):
        if language.lower() != "ippcode20":
            raise InvalidXmlException("Unknown language!")

    @staticmethod
    def __get_and_check_type(type_t):
        if not (returnType := Const.TYPES.setdefault(type_t, None)):
            raise InvalidXmlException("Unknown 'type' of argument: '" + type_t + "'!")
        return returnType
