"""
@file: Stats.py
@data: 13.4.2020
@author: Martin Koči <xkocim05@stud.fit.vutbr.cz>

This file is for storing statistics about interpretation.
"""

import sys
from .exceptions import WritePermissionError


class Stats:
    """ This class is storing all data about interpretation and writing them to the file """
    def __init__(self, file):
        self.instructions = 0
        self.maximum_vars = 0
        self.file = file

    def maximum_count(self, count):
        """ @param count: this is current count of initialized variables in all frames
            This function is checking if count is new maximum or not.
        """
        self.maximum_vars = max(count, self.maximum_vars)

    def inc_instructions(self):
        """ This function is incrementing how much instructions were executed """
        self.instructions += 1

    def write_stats(self):
        """This function is writing stats to the file"""
        try:
            if self.file is not None:
                with open(self.file, "w") as file:
                    for arg in sys.argv:
                        if arg == "--insts":
                            file.write(str(self.instructions) + "\n")
                        elif arg == "--vars":
                            file.write(str(self.maximum_vars) + "\n")
        except PermissionError:
            raise WritePermissionError("Missing write permission!")
