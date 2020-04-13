"""
@file: Program.py
@data: 13.4.2020
@author: Martin Koči <xkocim05@stud.fit.vutbr.cz>

This file is a interpreter itself. Here are stored all data about interpretation and here is from all instructions
are executed.
"""

from .exceptions import InvalidCodeException
from .Stats import Stats


class Program:
    """ This class is for storing data about interpretation and executing program. """
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

    def __count_initialized_variables(self):
        count = 0
        for var in self.GF.keys():
            if self.GF[var] is not None:
                count += 1
        if self.TF is not None:
            for var in self.TF.keys():
                if self.TF[var] is not None:
                    count += 1

        for stack in self.LF:
            for var in stack.keys():
                if stack[var] is not None:
                    count += 1
        self.stats.maximum_count(count)

    def run_program(self, instructions: {}):
        """
        @param instructions: Stored instructions that will be executed.
        This is method for running the interpretation.
        """

        for idx, instruction in enumerate(instructions):
            if instruction.op_code == "LABEL":
                if instruction.arguments["arg1"].content in self.labels:
                    raise InvalidCodeException("Redefinition of label '" + instruction.arguments["arg1"].content + "!''")  # error 52
                self.labels[instruction.arguments["arg1"].content] = idx

        while self.instruction_pointer < len(instructions):
            self.stats.inc_instructions()
            instructions[self.instruction_pointer].execute()
            self.__count_initialized_variables()
            self.instruction_pointer += 1

    def __repr__(self):
        return f'''
        Instruction pointer: {self.instruction_pointer}
        Instructions executed: {self.stats.instructions}
        Global Frame: {str(self.GF)}
        Local Frame: {str(self.LF)}
        Temporary Frame: {str(self.TF)}
        Call Stack: {str(self.call_stack)}
        Data Stack: {str(self.data_stack)}
        '''
