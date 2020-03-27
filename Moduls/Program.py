

class Program:
    def __init__(self):
        self.GF = {}
        self.LF = []
        self.TF = None
        self.call_stack = []
        self.data_stack = []
        self.labels = {}
        self.instruction_pointer = 1

    def run_program(self, instructions: {}):

        for instruction in instructions:
            if instructions[instruction].op_code == "LABEL":
                self.labels[instructions[instruction].arguments["arg1"]] = instruction

        while self.instruction_pointer <= len(instructions):
            instructions[self.instruction_pointer].execute()
            self.instruction_pointer += 1
