from .exceptions import InvalidCodeException
from .Stats import Stats


class Program:
    def __init__(self, input_data: dict, stats: Stats):
        self.GF = {}
        self.LF = []
        self.TF = None
        self.call_stack = []
        self.data_stack = []
        self.labels = {}
        self.instruction_pointer = 0
        self.input_data = input_data
        self.stats = stats

    def run_program(self, instructions: {}):

        for idx, instruction in enumerate(instructions):
            if instruction.op_code == "LABEL":
                if instruction.arguments["arg1"].content in self.labels:
                    raise InvalidCodeException("Redefinition of label '" + instruction.arguments["arg1"].content + "!''")  # error 52
                self.labels[instruction.arguments["arg1"].content] = idx

        while self.instruction_pointer < len(instructions):
            instructions[self.instruction_pointer].execute()
            self.stats.inc_instructions()
            self.instruction_pointer += 1

    def __repr__(self):
        # TODO add text to this...
        return ""
