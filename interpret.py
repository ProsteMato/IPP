from Moduls.XmlParser import XmlParser
import Moduls.Const as Const
from Moduls.Program import Program
import xml.etree.ElementTree as elementTree
import sys


try:
    program = Program()
    xmlParser = XmlParser("./Tests/int-only/stack_test.src", program)
    instructions = xmlParser.parse()
    program.run_program(instructions)
except elementTree.ParseError:
    print("Invalid XML!", file=sys.stderr)
    sys.exit(Const.INVALID_XML_ERROR)
except Const.InvalidXmlException:
    print("lex error", file=sys.stderr)
    sys.exit(Const.LEX_SYNTAX_ERROR)
sys.exit(0)
