from .Variable import Variable
from .Constant import Constant
from .Label import Label
from .TypeT import TypeT
from .Const import InvalidXmlException, INSTRUCTIONS


class Instruction:

    def __init__(self, program, op_code, arguments):
        self.op_code = op_code
        self.arguments = arguments
        self.program = program

    def execute(self):
        self.check_arguments()
        (eval("self." + self.op_code))()

    def check_arguments(self):
        required_argument_type = INSTRUCTIONS.setdefault(self.op_code, [])
        for index in range(1, len(required_argument_type) + 1):
            argument_name = "arg" + str(index)
            if argument_name not in self.arguments:
                raise InvalidXmlException
            if not isinstance(self.arguments[argument_name], required_argument_type[index - 1]):
                raise InvalidXmlException

    def var_exists(self, var):
        if var.scope == "GF":
            return var.name in self.program.GF
        elif var.scope == "LF":
            return len(self.program.LF[-1]) != 0 and var.name in self.program.LF[-1]
        else:
            return var.name in self.program.TF

    def load_symbol(self, symbol):
        if isinstance(symbol, Variable):
            return self.load_from_variable(symbol)
        else:
            return {"value": symbol.content, "type": symbol.type}

    def load_from_variable(self, var):
        if not self.var_exists(var):
            raise Exception  # error 54
        if var.scope == "GF":
            return self.program.GF[var.name]
        elif var.scope == "LF":
            return self.program.LF[-1][var.name]
        else:
            return self.program.TF[var.name]

    def store_to_variable(self, var, content):
        if not self.var_exists(var):
            raise Exception  # error 54
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
            raise Exception  # error 55
        self.program.LF.append(self.program.TF)
        self.program.TF = None

    def POPFRAME(self):
        if len(self.program.LF) == 0:
            raise Exception  # error 55
        self.program.TF = self.program.LF.pop()

    def CALL(self):
        self.program.call_stack.append(self.program.instruction_pointer + 1)
        self.program.instruction_pointer = self.program.labels[self.arguments["arg1"]]

    def RETURN(self):
        if len(self.program.call_stack) == 0:
            raise Exception  # error 56
        self.program.instruction_pointer = self.program.call_stack.pop()

    def PUSHS(self):
        content = self.load_symbol(self.arguments["arg1"])
        self.program.data_stack.append(content)

    def POPS(self):
        if len(self.program.data_stack) == 0:
            raise Exception  # error 56
        self.store_to_variable(self.arguments["arg1"], self.program.data_stack.pop())

    def INT2CHAR(self):
        argument1 = self.arguments["arg1"]
        argument2 = self.arguments["arg2"]
        content = self.load_symbol(argument2)

        if content["type"] != "int":
            raise Exception # error 58
        try:
            self.store_to_variable(argument1, {"value": chr(int(content["value"])), "type": "string"})
        except ValueError:
            raise Exception  # error 58

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
            raise Exception  # error 52
        if argument1.scope == "GF":
            self.program.GF[argument1.name] = None
        elif argument1.scope == "LF":
            self.program.LF[argument1.name] = None
        elif argument1.scope == "TF":
            self.program.TF[-1][argument1.name] = None




