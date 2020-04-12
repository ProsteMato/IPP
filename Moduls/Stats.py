import sys
from .exceptions import WritePermissionError

class Stats:

    def __init__(self, file):
        self.instructions = 0
        self.variables_GF = {}
        self.variables_LF = {}
        self.variables_TF = {}
        self.file = file

    def add_variable_gf(self, var):
        self.variables_GF[var] = False

    def add_variable_lf(self, var):
        self.variables_LF[var] = False

    def add_variable_tf(self, var):
        self.variables_TF[var] = False

    def initialized_variable_gf(self, var):
        self.variables_GF[var] = True

    def initialized_variable_lf(self, var):
        self.variables_LF[var] = True

    def initialized_variable_tf(self, var):
        self.variables_TF[var] = True

    def count_initialized(self):
        count = 0
        for variable in self.variables_GF:
            if self.variables_GF[variable]:
                count += 1
        for variable1 in self.variables_LF:
            if self.variables_LF[variable1]:
                count += 1
        for variable2 in self.variables_TF:
            if self.variables_TF[variable2]:
                count += 1
        return count

    def inc_instructions(self):
        self.instructions += 1

    def write_stats(self):
        try:
            with open(self.file, "w") as file:
                for arg in sys.argv:
                    if arg == "--insts":
                        file.write(str(self.instructions) + "\n")
                    elif arg == "--vars":
                        file.write(str(self.count_initialized()) + "\n")
        except PermissionError:
            raise WritePermissionError("Missing write permission!")
