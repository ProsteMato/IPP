from Moduls.XmlParser import XmlParser
from Moduls.Argument import Argument
import sys

xmlParser = XmlParser("./Tests/int-only/stack_test.src")
instructions = xmlParser.parse()
print(instructions)

sys.exit(0)
