"""
@file: Instruction.py
@data: 13.4.2020
@author: Martin Koči <xkocim05@stud.fit.vutbr.cz>

This file is for storing individual instructions and then executing them
"""
import sys
from .Variable import Variable
from .Constant import Constant
from .Label import Label
from .TypeT import TypeT
from .exceptions import *
from .Const import INSTRUCTIONS


class Instruction:
    """ This class is for storing instruction and executing them """
    def __init__(self, program, stats, order, op_code, arguments):
        self.order = order
        self.op_code = op_code
        self.arguments = arguments
        self.program = program
        self.stats = stats

    def execute(self):
        """ This function is checking arguments of instruction and executing it """
        self.__check_arguments()
        (eval('self._Instruction__' + self.op_code))()

    def __check_arguments(self):
        required_argument_type = INSTRUCTIONS.setdefault(self.op_code, [])
        if len(required_argument_type) != len(self.arguments):
            raise InvalidXmlException("More operands than needed!")
        for index, type in enumerate(required_argument_type):
            argument_name = "arg" + str(index + 1)
            if argument_name not in self.arguments:
                raise InvalidXmlException("Invalid name of argument element")
            if not isinstance(self.arguments[argument_name], type):
                raise InvalidXmlException("ASI INY ERROR TODO:")

    def __var_exists(self, var):
        if var.scope == "GF":
            return var.name in self.program.GF
        elif var.scope == "LF":
            if len(self.program.LF) == 0:
                raise InvalidFrameException("Accessing to non-existing frame!")
            return var.name in self.program.LF[-1]
        elif var.scope == "TF":
            if self.program.TF is None:
                raise InvalidFrameException("Accessing to non-existing frame!")
            return var.name in self.program.TF

    def __load_symbol(self, symbol):
        if isinstance(symbol, Variable):
            return self.__load_from_variable(symbol)
        elif isinstance(symbol, Constant):
            return {"value": symbol.content, "type": symbol.type}

    def __load_from_variable(self, var):
        if not self.__var_exists(var):
            raise NonExistingVarException("Trying to read from undefined variable!")  # error 54
        if var.scope == "GF":
            if self.program.GF[var.name] is None:
                raise MissingValueException("Accessing empty value!")
            return self.program.GF[var.name]
        elif var.scope == "LF":
            if self.program.LF[-1][var.name] is None:
                raise MissingValueException("Accessing empty value!")
            return self.program.LF[-1][var.name]
        else:
            if self.program.TF[var.name] is None:
                raise MissingValueException("Accessing empty value!")
            return self.program.TF[var.name]

    def __store_to_variable(self, var, content):
        if not self.__var_exists(var):
            raise NonExistingVarException("Trying to read from undefined variable!")  # error 54
        if var.scope == "GF":
            self.program.GF[var.name] = content
        elif var.scope == "LF":
            self.program.LF[-1][var.name] = content
        else:
            self.program.TF[var.name] = content

    def __CREATEFRAME(self):
        self.program.TF = {}

    def __PUSHFRAME(self):
        if self.program.TF is None:
            raise InvalidFrameException("Reading from empty frame!")  # error 55
        self.program.LF.append(self.program.TF)
        self.program.TF = None

    def __POPFRAME(self):
        if len(self.program.LF) == 0:
            raise InvalidFrameException("Reading from empty frame!")  # error 55
        self.program.TF = self.program.LF.pop()

    def __CALL(self):
        self.program.call_stack.append(self.program.instruction_pointer + 1)
        self.__JUMP()

    def __RETURN(self):
        if len(self.program.call_stack) == 0:
            raise MissingValueException("Missing value in stack")  # error 56
        self.program.instruction_pointer = self.program.call_stack.pop() - 1

    def __PUSHS(self):
        content = self.__load_symbol(self.arguments["arg1"])
        self.program.data_stack.append(content)

    def __pops(self):
        if len(self.program.data_stack) == 0:
            raise MissingValueException("Missing value in stack")  # error 56
        return self.program.data_stack.pop()

    def __POPS(self):
        self.__store_to_variable(self.arguments["arg1"], self.__pops())

    def __CLEARS(self):
        self.program.data_stack = []

    def __INT2CHAR(self):
        symbol = self.__load_symbol(self.arguments["arg2"])

        if symbol["type"] != "int":
            raise BadOperandTypeException("Invalid type of argument")  # error 53

        try:
            self.__store_to_variable(self.arguments["arg1"], {"value": chr(symbol["value"]), "type": "string"})
        except ValueError:
            raise InvalidStringOperationException("Invalid order value!")  # error 58

    def __INT2CHARS(self):
        symbol = self.__pops()

        if symbol["type"] != "int":
            raise BadOperandTypeException("Invalid type of argument")  # error 53

        try:
            self.program.data_stack.append({"value": chr(symbol["value"]), "type": "string"})
        except ValueError:
            raise InvalidStringOperationException("Invalid order value!")  # error 58

    def __WRITE(self):
        argument1 = self.arguments["arg1"]
        content = self.__load_symbol(argument1)
        if content["type"] in ["int", "type", "string"]:
            print(content["value"], end="")
        elif content["type"] == "bool":
            print("true" if content["value"] else "false", end="")
        elif content["type"] == "nil":
            print("", end="")
        elif content["type"] == "float":
            print(float.hex(content["value"]), end="")

    def __MOVE(self):
        argument1 = self.arguments["arg1"]
        argument2 = self.arguments["arg2"]

        content = self.__load_symbol(argument2)
        self.__store_to_variable(argument1, content)

    def __DEFVAR(self):
        argument1 = self.arguments["arg1"]
        if self.__var_exists(argument1):
            raise InvalidCodeException("Variable '" + argument1.name + "' is already defined!")  # error 54
        if argument1.scope == "GF":
            self.program.GF[argument1.name] = None
        elif argument1.scope == "LF":
            self.program.LF[-1][argument1.name] = None
        elif argument1.scope == "TF":
            self.program.TF[argument1.name] = None

    def __arithmetic_check(self, symbol1, symbol2):
        if self.op_code == "DIV" and (symbol1["type"] != "float" or symbol2["type"] != "float"):
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53
        if self.op_code == "IDIV" and (symbol1["type"] != "int" or symbol2["type"] != "int"):
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53
        elif not (symbol1["type"] == "int" and symbol2["type"] == "int" or symbol1["type"] == "float" and symbol2[
            "type"] == "float"):
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

    def __arithmetic_operands(self):
        symbol1 = self.__load_symbol(self.arguments["arg2"])
        symbol2 = self.__load_symbol(self.arguments["arg3"])

        self.__arithmetic_check(symbol1, symbol2)

        return self.arguments["arg1"], symbol1, symbol2, "int" if symbol1["type"] == "int" else "float"

    def __ADD(self):
        variable, symbol1, symbol2, type_t = self.__arithmetic_operands()
        self.__store_to_variable(variable, {"value": symbol1["value"] + symbol2["value"], "type": type_t})

    def __SUB(self):
        variable, symbol1, symbol2, type_t = self.__arithmetic_operands()
        self.__store_to_variable(variable, {"value": symbol1["value"] - symbol2["value"], "type": type_t})

    def __MUL(self):
        variable, symbol1, symbol2, type_t = self.__arithmetic_operands()
        self.__store_to_variable(variable, {"value": symbol1["value"] * symbol2["value"], "type": type_t})

    def __DIV(self):
        variable, symbol1, symbol2, type_t = self.__arithmetic_operands()

        if symbol2["value"] == 0.0:
            raise BadValueException("Dividing be zero!")  # error 57

        self.__store_to_variable(variable, {"value": symbol1["value"] / symbol2["value"], "type": type_t})

    def __IDIV(self):
        variable, symbol1, symbol2, type_t = self.__arithmetic_operands()

        if symbol2["value"] == 0:
            raise BadValueException("Dividing be zero!")  # error 57

        self.__store_to_variable(self.arguments["arg1"],
                                 {"value": symbol1["value"] // symbol2["value"], "type": type_t})

    def __arithmetic_operands_stack(self):
        symbol2 = self.__pops()
        symbol1 = self.__pops()

        self.__arithmetic_check(symbol1, symbol2)

        return symbol1, symbol2, "int" if symbol1["type"] == "int" else "float"

    def __ADDS(self):
        symbol1, symbol2, type_t = self.__arithmetic_operands_stack()
        self.program.data_stack.append({"value": symbol1["value"] + symbol2["value"], "type": type_t})

    def __SUBS(self):
        symbol1, symbol2, type_t = self.__arithmetic_operands_stack()
        self.program.data_stack.append({"value": symbol1["value"] - symbol2["value"], "type": type_t})

    def __MULS(self):
        symbol1, symbol2, type_t = self.__arithmetic_operands_stack()
        self.program.data_stack.append({"value": symbol1["value"] * symbol2["value"], "type": type_t})

    def __DIVS(self):
        symbol1, symbol2, type_t = self.__arithmetic_operands_stack()

        if symbol2["value"] == 0.0:
            raise BadValueException("Dividing be zero!")  # error 57

        self.program.data_stack.append({"value": symbol1["value"] / symbol2["value"], "type": type_t})

    def __IDIVS(self):
        symbol1, symbol2, type_t = self.__arithmetic_operands_stack()

        if symbol2["value"] == 0:
            raise BadValueException("Dividing be zero!")  # error 57

        self.program.data_stack.append({"value": symbol1["value"] // symbol2["value"], "type": type_t})

    def __comparison_operands(self):
        symbol1 = self.__load_symbol(self.arguments["arg2"])
        symbol2 = self.__load_symbol(self.arguments["arg3"])

        if symbol1["type"] != symbol2["type"] or symbol1["type"] == "nil" or symbol2["type"] == "nil":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        return self.arguments["arg1"], symbol1, symbol2

    def __LT(self):
        variable, symbol1, symbol2 = self.__comparison_operands()
        self.__store_to_variable(variable, {"value": symbol1["value"] < symbol2["value"], "type": "bool"})

    def __GT(self):
        variable, symbol1, symbol2 = self.__comparison_operands()
        self.__store_to_variable(variable, {"value": symbol1["value"] > symbol2["value"], "type": "bool"})

    def __equal(self):
        symbol1 = self.__load_symbol(self.arguments["arg2"])
        symbol2 = self.__load_symbol(self.arguments["arg3"])

        if symbol1["type"] != "nil" and symbol2["type"] != "nil" and symbol1["type"] != symbol2["type"]:
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        return symbol1["value"] == symbol2["value"]

    def __EQ(self):
        self.__store_to_variable(self.arguments["arg1"], {"value": self.__equal(), "type": "bool"})

    def __comparison_operands_stack(self):
        symbol2 = self.__pops()
        symbol1 = self.__pops()

        if symbol1["type"] != symbol2["type"] or symbol1["type"] == "nil" or symbol2["type"] == "nil":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        return symbol1, symbol2

    def __LTS(self):
        symbol1, symbol2 = self.__comparison_operands_stack()
        self.program.data_stack.append({"value": symbol1["value"] < symbol2["value"], "type": "bool"})

    def __GTS(self):
        symbol1, symbol2 = self.__comparison_operands_stack()
        self.program.data_stack.append({"value": symbol1["value"] > symbol2["value"], "type": "bool"})

    def __equal_stack(self):
        symbol2 = self.__pops()
        symbol1 = self.__pops()

        if symbol1["type"] != "nil" and symbol2["type"] != "nil" and symbol1["type"] != symbol2["type"]:
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        return symbol1["value"] == symbol2["value"]

    def __EQS(self):
        self.program.data_stack.append({"value": self.__equal_stack(), "type": "bool"})

    def __logic_operands(self):
        symbol1 = self.__load_symbol(self.arguments["arg2"])
        symbol2 = self.__load_symbol(self.arguments["arg3"])

        if symbol1["type"] != "bool" or symbol2["type"] != "bool":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        return self.arguments["arg1"], symbol1, symbol2

    def __AND(self):
        variable, symbol1, symbol2 = self.__logic_operands()
        self.__store_to_variable(self.arguments["arg1"],
                                 {"value": symbol1["value"] and symbol2["value"], "type": "bool"})

    def __OR(self):
        variable, symbol1, symbol2 = self.__logic_operands()
        self.__store_to_variable(self.arguments["arg1"],
                                 {"value": symbol1["value"] or symbol2["value"], "type": "bool"})

    def __NOT(self):
        symbol1 = self.__load_symbol(self.arguments["arg2"])

        if symbol1["type"] != "bool":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        self.__store_to_variable(self.arguments["arg1"], {"value": not symbol1["value"], "type": "bool"})

    def __logic_operands_stack(self):
        symbol2 = self.__pops()
        symbol1 = self.__pops()

        if symbol1["type"] != "bool" or symbol2["type"] != "bool":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        return symbol1, symbol2

    def __ANDS(self):
        symbol1, symbol2 = self.__logic_operands_stack()
        self.program.data_stack.append({"value": symbol1["value"] and symbol2["value"], "type": "bool"})

    def __ORS(self):
        symbol1, symbol2 = self.__logic_operands_stack()
        self.program.data_stack.append({"value": symbol1["value"] or symbol2["value"], "type": "bool"})

    def __NOTS(self):
        symbol1 = self.__pops()

        if symbol1["type"] != "bool":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        self.program.data_stack.append({"value": not symbol1["value"], "type": "bool"})

    def __string_operands(self):
        symbol1 = self.__load_symbol(self.arguments["arg2"])
        symbol2 = self.__load_symbol(self.arguments["arg3"])
        if symbol1["type"] != "string" or symbol2["type"] != "int":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53
        if len(symbol1["value"]) <= symbol2["value"] or symbol2["value"] < 0:
            raise InvalidStringOperationException("Bad indexing in string!")  # error 58
        return self.arguments["arg1"], symbol1, symbol2

    def __STRI2INTS(self):
        symbol2 = self.__pops()
        symbol1 = self.__pops()
        if symbol1["type"] != "string" or symbol2["type"] != "int":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53
        if len(symbol1["value"]) <= symbol2["value"] or symbol2["value"] < 0:
            raise InvalidStringOperationException("Bad indexing in string!")  # error 58
        self.program.data_stack.append({"value": ord(symbol1["value"][symbol2["value"]]), "type": "int"})

    def __STRI2INT(self):
        variable, symbol1, symbol2 = self.__string_operands()
        self.__store_to_variable(variable, {"value": ord(symbol1["value"][symbol2["value"]]), "type": "int"})

    def __GETCHAR(self):
        variable, symbol1, symbol2 = self.__string_operands()
        self.__store_to_variable(variable, {"value": symbol1["value"][symbol2["value"]], "type": "string"})

    def __FLOAT2INT(self):
        symbol1 = self.__load_symbol(self.arguments["arg2"])

        if symbol1["type"] != "float":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        self.__store_to_variable(self.arguments["arg1"], {"value": int(symbol1["value"]), "type": "int"})

    def __INT2FLOAT(self):
        symbol1 = self.__load_symbol(self.arguments["arg2"])

        if symbol1["type"] != "int":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        self.__store_to_variable(self.arguments["arg1"], {"value": float(symbol1["value"]), "type": "float"})

    def __FLOAT2INTS(self):
        symbol1 = self.__pops()

        if symbol1["type"] != "float":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        self.program.data_stack.append({"value": int(symbol1["value"]), "type": "int"})

    def __INT2FLOATS(self):
        symbol1 = self.__pops()

        if symbol1["type"] != "int":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        self.program.data_stack.append({"value": float(symbol1["value"]), "type": "float"})

    def __SETCHAR(self):
        var = self.__load_symbol(self.arguments["arg1"])
        symbol1 = self.__load_symbol(self.arguments["arg2"])
        symbol2 = self.__load_symbol(self.arguments["arg3"])

        if var["type"] != "string" or symbol1["type"] != "int" or symbol2["type"] != "string":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        if len(var["value"]) <= symbol1["value"] or len(symbol2["value"]) == 0 or symbol1["value"] < 0:
            raise InvalidStringOperationException("Bad indexing in string!")  # error 58

        new = list(var["value"])
        new[symbol1["value"]] = symbol2["value"][0]

        self.__store_to_variable(self.arguments["arg1"], {"value": ''.join(new), "type": "string"})

    def __READ(self):
        type_t = self.arguments["arg2"]

        if type_t.content != "bool" and type_t.content != "string" and type_t.content != "int" and type_t.content != "float":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53 maybe another

        if self.program.input_data["file"] == sys.stdin:
            input_value = input()
        else:
            if len(self.program.input_data["data"]) > 0:
                input_value = self.program.input_data["data"].pop(0)
            else:
                self.__store_to_variable(self.arguments["arg1"], {"value": None, "type": "nil"})
                return

        if type_t.content == "bool":
            input_value = {"value": input_value.lower() == "true", "type": "bool"}
        elif type_t.content == "string":
            input_value = {"value": input_value, "type": "string"}
        elif type_t.content == "float":
            try:
                if input_value.count("0x") == 1:
                    input_value = {"value": float.fromhex(input_value), "type": "float"}
                else:
                    input_value = {"value": float(input_value), "type": "float"}
            except ValueError:
                input_value = {"value": None, "type": "nil"}
        elif type_t.content == "int":
            try:
                if input_value.count("0x") == 1:
                    input_value = {"value": int(input_value, 16), "type": "int"}
                else:
                    input_value = {"value": int(input_value), "type": "int"}
            except ValueError:
                input_value = {"value": None, "type": "nil"}

        self.__store_to_variable(self.arguments["arg1"], input_value)

    def __CONCAT(self):
        symbol1 = self.__load_symbol(self.arguments["arg2"])
        symbol2 = self.__load_symbol(self.arguments["arg3"])
        if symbol1["type"] != "string" or symbol2["type"] != "string":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53
        self.__store_to_variable(self.arguments["arg1"],
                                 {"value": symbol1["value"] + symbol2["value"], "type": "string"})

    def __STRLEN(self):
        symbol1 = self.__load_symbol(self.arguments["arg2"])
        if symbol1["type"] != "string":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53
        self.__store_to_variable(self.arguments["arg1"], {"value": len(symbol1["value"]), "type": "int"})

    def __TYPE(self):
        symbol1 = self.arguments["arg2"]
        if isinstance(symbol1, Variable):
            if not self.__var_exists(symbol1):
                raise NonExistingVarException("Trying to read from undefined variable!")  # error 54
            if symbol1.scope == "GF":
                symbol1 = self.program.GF[symbol1.name]
            elif symbol1.scope == "LF":
                symbol1 = self.program.LF[-1][symbol1.name]
            else:
                symbol1 = self.program.TF[symbol1.name]
        elif isinstance(symbol1, Constant):
            symbol1 = {"value": symbol1.content, "type": symbol1.type}

        if symbol1 is None:
            self.__store_to_variable(self.arguments["arg1"], {"value": "", "type": "string"})
        else:
            self.__store_to_variable(self.arguments["arg1"], {"value": symbol1["type"], "type": "string"})

    def __LABEL(self):
        pass

    def __check_label(self):
        label = self.arguments["arg1"]
        if label.content not in self.program.labels:
            raise InvalidCodeException("Undefined label: '" + label.content + "'!")  # error 52
        return label

    def __JUMP(self):
        label = self.__check_label()
        self.program.instruction_pointer = self.program.labels[label.content] - 1

    def __JUMPIFEQ(self):
        label = self.__check_label()
        if self.__equal():
            self.program.instruction_pointer = self.program.labels[label.content] - 1

    def __JUMPIFNEQ(self):
        label = self.__check_label()
        if not self.__equal():
            self.program.instruction_pointer = self.program.labels[label.content] - 1

    def __JUMPIFEQS(self):
        label = self.__check_label()
        if self.__equal_stack():
            self.program.instruction_pointer = self.program.labels[label.content] - 1

    def __JUMPIFNEQS(self):
        label = self.__check_label()
        if not self.__equal_stack():
            self.program.instruction_pointer = self.program.labels[label.content] - 1

    def __EXIT(self):
        symbol = self.__load_symbol(self.arguments["arg1"])
        if symbol["type"] != "int":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53
        if symbol["value"] < 0 or symbol["value"] > 49:
            raise BadValueException("Invalid exit value!")  # error 57
        if symbol["value"] == 0:
            self.stats.write_stats()
        sys.exit(symbol["value"])

    def __DPRINT(self):
        symbol = self.__load_symbol(self.arguments["arg1"])
        if symbol["type"] in ["int", "type"]:
            print(symbol["value"], end="", file=sys.stderr)
        elif symbol["type"] == "string":
            print(symbol["value"], end="", file=sys.stderr)
        elif symbol["type"] == "bool":
            print("true" if symbol["value"] else "false", end="", file=sys.stderr)
        elif symbol["type"] == "nil":
            print("", end="", file=sys.stderr)

    def __BREAK(self):
        print(self.program.__repr__(), file=sys.stderr)
