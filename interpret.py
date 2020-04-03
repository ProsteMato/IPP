from Moduls.XmlParser import XmlParser
import Moduls.Const as Const
from Moduls.Program import Program
import xml.etree.ElementTree as elementTree
from Moduls.exceptions import *
import argparse
import sys

parser = argparse.ArgumentParser()
parser.add_argument("--source", default=sys.stdin)
parser.add_argument("--input", default=sys.stdin)

parsed_args = parser.parse_args()

if parsed_args.source == sys.stdin and parsed_args.input == sys.stdin:
    print("no parameters")

input_file = dict()
input_file["file"] = parsed_args.input
input_file["data"] = []

if parsed_args.input != sys.stdin:
    with open(parsed_args.input) as file:
        input_file["data"] = file.read().splitlines()

try:
    program = Program(input_file)
    xmlParser = XmlParser(parsed_args.source, program)
    instructions = xmlParser.parse()
    program.run_program(instructions)
except elementTree.ParseError:
    print("Not well-formed XML file!", file=sys.stderr)
    sys.exit(Const.INVALID_XML_ERROR)
except (InvalidXmlException, InvalidCodeException, BadOperandTypeException,
        NonExistingVarException, InvalidFrameException, MissingValueException,
        BadValueException, InvalidStringOperationException) as e:
    print(e.msg, file=sys.stderr)
    sys.exit(e.error)
sys.exit(Const.ERROR_OK)
