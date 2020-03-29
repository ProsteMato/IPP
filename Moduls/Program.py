

class Program:
    def __init__(self, input_data: dict):
        self.GF = {}
        self.LF = []
        self.TF = None
        self.call_stack = []
        self.data_stack = []
        self.labels = {}
        self.instruction_pointer = 0
        self.input_data = input_data

    def run_program(self, instructions: {}):

        for idx, instruction in enumerate(instructions):
            if instruction.op_code == "LABEL":
                if instruction.arguments["arg1"] in self.labels:
                    raise Exception  # error 52
                self.labels[instruction.arguments["arg1"]] = idx

        while self.instruction_pointer < len(instructions):
            instructions[self.instruction_pointer].execute()
            self.instruction_pointer += 1

    def __repr__(self):
        # TODO add text to this...
        return ""
