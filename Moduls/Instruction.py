import sys
from .Variable import Variable
from .Constant import Constant
from .Label import Label
from .TypeT import TypeT
from .exceptions import *
from .Const import INSTRUCTIONS


class Instruction:

    def __init__(self, program, order, op_code, arguments):
        self.order = order
        self.op_code = op_code
        self.arguments = arguments
        self.program = program

    def execute(self):
        self.check_arguments()
        (eval("self." + self.op_code))()

    def check_arguments(self):
        required_argument_type = INSTRUCTIONS.setdefault(self.op_code, [])
        for index, type in enumerate(required_argument_type):
            argument_name = "arg" + str(index + 1)
            if argument_name not in self.arguments:
                raise InvalidXmlException("Invalid name of argument element")
            if not isinstance(self.arguments[argument_name], type):
                raise InvalidXmlException("ASI INY ERROR TODO:")

    def var_exists(self, var):
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

    def load_symbol(self, symbol):
        if isinstance(symbol, Variable):
            return self.load_from_variable(symbol)
        elif isinstance(symbol, Constant):
            return {"value": symbol.content, "type": symbol.type}

    def load_from_variable(self, var):
        if not self.var_exists(var):
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

    def store_to_variable(self, var, content):
        if not self.var_exists(var):
            raise NonExistingVarException("Trying to read from undefined variable!")  # error 54
        if var.scope == "GF":
            self.program.GF[var.name] = content
        elif var.scope == "LF":
            self.program.LF[-1][var.name] = content
        else:
            self.program.TF[var.name] = content

    def CREATEFRAME(self):
        self.program.TF = {}

    def PUSHFRAME(self):
        if self.program.TF is None:
            raise InvalidFrameException("Reading from empty frame!")  # error 55
        self.program.LF.append(self.program.TF)
        self.program.TF = None

    def POPFRAME(self):
        if len(self.program.LF) == 0:
            raise InvalidFrameException("Reading from empty frame!")  # error 55
        self.program.TF = self.program.LF.pop()

    def CALL(self):
        label = self.arguments["arg1"].content
        self.program.call_stack.append(self.program.instruction_pointer + 1)
        if label not in self.program.labels:
            raise InvalidCodeException("Undefined label: '" + label + "'!")  # error 52
        self.program.instruction_pointer = self.program.labels[label]

    def RETURN(self):
        if len(self.program.call_stack) == 0:
            raise MissingValueException("Missing value in stack")  # error 56
        self.program.instruction_pointer = self.program.call_stack.pop() - 1

    def PUSHS(self):
        content = self.load_symbol(self.arguments["arg1"])
        self.program.data_stack.append(content)

    def POPS(self):
        if len(self.program.data_stack) == 0:
            raise MissingValueException("Missing value in stack")  # error 56
        self.store_to_variable(self.arguments["arg1"], self.program.data_stack.pop())

    def INT2CHAR(self):
        argument1 = self.arguments["arg1"]
        argument2 = self.arguments["arg2"]
        content = self.load_symbol(argument2)

        if content["type"] != "int":
            raise BadOperandTypeException("Invalid type of argument")  # error 53

        try:
            self.store_to_variable(argument1, {"value": chr(content["value"]), "type": "string"})
        except ValueError:
            raise InvalidStringOperationException("Invalid order value!")  # error 58

    def WRITE(self):
        argument1 = self.arguments["arg1"]
        content = self.load_symbol(argument1)
        if content["type"] in ["int", "type"]:
            print(content["value"], end="")
        elif content["type"] == "string":
            print(content["value"], end="")
        elif content["type"] == "bool":
            print("true" if content["value"] else "false", end="")
        elif content["type"] == "nil":
            print("", end="")

    def MOVE(self):
        argument1 = self.arguments["arg1"]
        argument2 = self.arguments["arg2"]

        content = self.load_symbol(argument2)
        self.store_to_variable(argument1, content)

    def DEFVAR(self):
        argument1 = self.arguments["arg1"]
        if self.var_exists(argument1):
            raise InvalidCodeException("Variable '" + argument1.name + "' is already defined!")  # error 54
        if argument1.scope == "GF":
            self.program.GF[argument1.name] = None
        elif argument1.scope == "LF":
            self.program.LF[-1][argument1.name] = None
        elif argument1.scope == "TF":
            self.program.TF[argument1.name] = None

    def ADD(self):
        symbol1 = self.load_symbol(self.arguments["arg2"])
        symbol2 = self.load_symbol(self.arguments["arg3"])

        if symbol1["type"] != "int" or symbol2["type"] != "int":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        self.store_to_variable(self.arguments["arg1"], {"value": symbol1["value"] + symbol2["value"], "type": "int"})

    def SUB(self):
        symbol1 = self.load_symbol(self.arguments["arg2"])
        symbol2 = self.load_symbol(self.arguments["arg3"])

        if symbol1["type"] != "int" or symbol2["type"] != "int":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        self.store_to_variable(self.arguments["arg1"], {"value": symbol1["value"] - symbol2["value"], "type": "int"})

    def MUL(self):
        symbol1 = self.load_symbol(self.arguments["arg2"])
        symbol2 = self.load_symbol(self.arguments["arg3"])

        if symbol1["type"] != "int" or symbol2["type"] != "int":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        self.store_to_variable(self.arguments["arg1"], {"value": symbol1["value"] * symbol2["value"], "type": "int"})

    def IDIV(self):
        symbol1 = self.load_symbol(self.arguments["arg2"])
        symbol2 = self.load_symbol(self.arguments["arg3"])

        if symbol1["type"] != "int" or symbol2["type"] != "int":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        if symbol2["value"] == 0:
            raise BadValueException("Dividing be zero!")  # error 57

        self.store_to_variable(self.arguments["arg1"], {"value": symbol1["value"] // symbol2["value"], "type": "int"})

    def LT(self):
        symbol1 = self.load_symbol(self.arguments["arg2"])
        symbol2 = self.load_symbol(self.arguments["arg3"])

        if symbol1["type"] != symbol2["type"] or symbol1["type"] == "nil" or symbol2["type"] == "nil":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        self.store_to_variable(self.arguments["arg1"], {"value": symbol1["value"] < symbol2["value"], "type": "bool"})

    def GT(self):
        symbol1 = self.load_symbol(self.arguments["arg2"])
        symbol2 = self.load_symbol(self.arguments["arg3"])

        if symbol1["type"] != symbol2["type"] or symbol1["type"] == "nil" or symbol2["type"] == "nil":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        self.store_to_variable(self.arguments["arg1"], {"value": symbol1["value"] > symbol2["value"], "type": "bool"})

    def EQ(self):
        symbol1 = self.load_symbol(self.arguments["arg2"])
        symbol2 = self.load_symbol(self.arguments["arg3"])

        if symbol1["type"] != "nil" and symbol2["type"] != "nil" and symbol1["type"] != symbol2["type"]:
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        self.store_to_variable(self.arguments["arg1"], {"value": symbol1["value"] == symbol2["value"], "type": "bool"})

    def AND(self):
        symbol1 = self.load_symbol(self.arguments["arg2"])
        symbol2 = self.load_symbol(self.arguments["arg3"])

        if symbol1["type"] != "bool" or symbol2["type"] != "bool":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        self.store_to_variable(self.arguments["arg1"], {"value": symbol1["value"] and symbol2["value"], "type": "bool"})

    def OR(self):
        symbol1 = self.load_symbol(self.arguments["arg2"])
        symbol2 = self.load_symbol(self.arguments["arg3"])

        if symbol1["type"] != "bool" or symbol2["type"] != "bool":
            raise BadOperandTypeException("Incorrect 'type' of argument!")  # error 53

        self.store_to_variable(self.arguments["arg1"], {"value": symbol1["value"] or symbol2["value"], "type": "bool"})

    def NOT(self):
        symbol1 = self.load_symbol(self.arguments["arg2"])

        if symbol1["type"] != "bool":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        self.store_to_variable(self.arguments["arg1"], {"value": not symbol1["value"], "type": "bool"})

    def STRI2INT(self):
        symbol1 = self.load_symbol(self.arguments["arg2"])
        symbol2 = self.load_symbol(self.arguments["arg3"])

        if symbol1["type"] != "string" or symbol2["type"] != "int":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        if len(symbol1["value"]) <= symbol2["value"] or symbol2["value"] < 0:
            raise InvalidStringOperationException("Bad indexing in string!")  # error 58

        self.store_to_variable(self.arguments["arg1"], {"value": ord(symbol1["value"][symbol2["value"]]), "type": "int"})

    def READ(self):
        type_t = self.arguments["arg2"]

        if type_t.content != "bool" and type_t.content != "string" and type_t.content != "int":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53 maybe another

        if self.program.input_data["file"] == sys.stdin:
            input_value = input()
        else:
            if len(self.program.input_data["data"]) > 0:
                input_value = self.program.input_data["data"].pop(0)
            else:
                self.store_to_variable(self.arguments["arg1"], {"value": None, "type": "nil"})
                return

        if type_t.content == "bool":
            input_value = {"value": input_value.lower() == "true", "type": "bool"}
        elif type_t.content == "string":
            input_value = {"value": input_value, "type": "string"}
        elif type_t.content == "int":
            try:
                input_value = {"value": int(input_value), "type": "int"}
            except ValueError:
                input_value = {"value": None, "type": "nil"}

        self.store_to_variable(self.arguments["arg1"], input_value)

    def CONCAT(self):
        symbol1 = self.load_symbol(self.arguments["arg2"])
        symbol2 = self.load_symbol(self.arguments["arg3"])

        if symbol1["type"] != "string" or symbol2["type"] != "string":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        self.store_to_variable(self.arguments["arg1"], {"value": symbol1["value"] + symbol2["value"], "type": "string"})

    def STRLEN(self):
        symbol1 = self.load_symbol(self.arguments["arg2"])

        if symbol1["type"] != "string":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        self.store_to_variable(self.arguments["arg1"], {"value": len(symbol1["value"]), "type": "int"})

    def GETCHAR(self):
        symbol1 = self.load_symbol(self.arguments["arg2"])
        symbol2 = self.load_symbol(self.arguments["arg3"])

        if symbol1["type"] != "string" or symbol2["type"] != "int":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        if len(symbol1["value"]) <= symbol2["value"] or symbol2["value"] < 0:
            raise InvalidStringOperationException("Bad indexing in string!")  # error 58

        self.store_to_variable(self.arguments["arg1"], {"value": symbol1["value"][symbol2["value"]], "type": "string"})

    def SETCHAR(self):
        var = self.load_symbol(self.arguments["arg1"])
        symbol1 = self.load_symbol(self.arguments["arg2"])
        symbol2 = self.load_symbol(self.arguments["arg3"])

        if var["type"] != "string" or symbol1["type"] != "int" or symbol2["type"] != "string":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        if len(var["value"]) <= symbol1["value"] or len(symbol2["value"]) == 0 or symbol1["value"] < 0:
            raise InvalidStringOperationException("Bad indexing in string!")  # error 58

        new = list(var["value"])
        new[symbol1["value"]] = symbol2["value"][0]

        self.store_to_variable(self.arguments["arg1"], {"value": ''.join(new), "type": "string"})

    def TYPE(self):
        symbol1 = self.arguments["arg2"]
        if isinstance(symbol1, Variable):
            if not self.var_exists(symbol1):
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
            self.store_to_variable(self.arguments["arg1"], {"value": "", "type": "string"})
        else:
            self.store_to_variable(self.arguments["arg1"], {"value": symbol1["type"], "type": "string"})

    def LABEL(self):
        pass

    def JUMP(self):
        label = self.arguments["arg1"]
        if label.content not in self.program.labels:
            raise InvalidCodeException("Undefined label: '" + label.content + "'!")  # error 52

        self.program.instruction_pointer = self.program.labels[label.content]

    def JUMPIFEQ(self):
        label = self.arguments["arg1"]
        symbol1 = self.load_symbol(self.arguments["arg2"])
        symbol2 = self.load_symbol(self.arguments["arg3"])

        if symbol1["type"] != "nil" and symbol2["type"] != "nil" and symbol1["type"] != symbol2["type"]:
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        if label.content not in self.program.labels:
            raise InvalidCodeException("Undefined label: '" + label.content + "'!")  # error 52

        if symbol1["value"] == symbol2["value"]:
            self.program.instruction_pointer = self.program.labels[label.content]

    def JUMPIFNEQ(self):
        label = self.arguments["arg1"]
        symbol1 = self.load_symbol(self.arguments["arg2"])
        symbol2 = self.load_symbol(self.arguments["arg3"])

        if symbol1["type"] != "nil" and symbol2["type"] != "nil" and symbol1["type"] != symbol2["type"]:
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        if label.content not in self.program.labels:
            raise InvalidCodeException("Undefined label: '" + label.content + "'!")  # error 52

        if symbol1["value"] != symbol2["value"]:
            self.program.instruction_pointer = self.program.labels[label.content]

    def EXIT(self):
        symbol = self.load_symbol(self.arguments["arg1"])

        if symbol["type"] != "int":
            raise BadOperandTypeException("Incorrect Type of argument!")  # error 53

        if symbol["value"] < 0 or symbol["value"] > 49:
            raise BadValueException("Invalid exit value!")  # error 57
        sys.exit(symbol["value"])

    def DPRINT(self):
        symbol = self.load_symbol(self.arguments["arg1"])
        if symbol["type"] in ["int", "type"]:
            print(symbol["value"], end="", file=sys.stderr)
        elif symbol["type"] == "string":
            print(symbol["value"], end="", file=sys.stderr)
        elif symbol["type"] == "bool":
            print("true" if symbol["value"] else "false", end="", file=sys.stderr)
        elif symbol["type"] == "nil":
            print("", end="", file=sys.stderr)

    def BREAK(self):
        print(self.program.__repr__(), file=sys.stderr)



