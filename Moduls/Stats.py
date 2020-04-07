import sys
from .exceptions import WritePermissionError

class Stats:

    def __init__(self, file):
        self.instructions = 0
        self.variables = {}
        self.file = file

    def add_variable(self, var):
        self.variables[var] = False

    def initialized_variable(self, var):
        self.variables[var] = True

    def count_initialized(self):
        count = 0
        for variable in self.variables:
            if self.variables[variable]:
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
