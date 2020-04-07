from Moduls.XmlParser import XmlParser
import Moduls.Const as Const
from Moduls.Program import Program
import xml.etree.ElementTree as elementTree
from Moduls.exceptions import *
from Moduls.Stats import Stats
import argparse
import sys

parser = argparse.ArgumentParser(add_help=False)
parser.add_argument("--source", default=sys.stdin)
parser.add_argument("--input", default=sys.stdin)
parser.add_argument("--insts", action='store_true')
parser.add_argument("--vars", action='store_true')
parser.add_argument("--help", action='store_true')
parser.add_argument("--stats", default=None)

parsed_args = parser.parse_args()

if parsed_args.help and len(sys.argv) == 2:
    print(Const.HELP)
    sys.exit(Const.ERROR_OK)
elif parsed_args.help and len(sys.argv) > 2:
    sys.exit(Const.MISSING_ARGUMENT)

if (parsed_args.insts or parsed_args.vars) and parsed_args.stats is None:
    print("Stats is required argument when vars or insts are used!", file=sys.stderr)
    sys.exit(Const.MISSING_ARGUMENT)

if parsed_args.source == sys.stdin and parsed_args.input == sys.stdin:
    print("Source or input argument must be presented!", file=sys.stderr)
    sys.exit(Const.MISSING_ARGUMENT)

input_file = dict()
input_file["file"] = parsed_args.input
input_file["data"] = []

try:
    if parsed_args.input != sys.stdin:
        with open(parsed_args.input) as file:
            input_file["data"] = file.read().splitlines()
except (FileNotFoundError, PermissionError):
    print("Source or input file does not exist or permission error!", file=sys.stderr)
    sys.exit(Const.FILE_DONT_EXIST_ERROR)

try:
    stats = Stats(parsed_args.stats)
    program = Program(input_file, stats)
    xmlParser = XmlParser(parsed_args.source, program, stats)
    instructions = xmlParser.parse()
    program.run_program(instructions)
    if stats.file is not None:
        stats.write_stats()
except WritePermissionError as e:
    print(e.msg, file=sys.stderr)
    sys.exit(e.error)
except (FileNotFoundError, PermissionError):
    print("File does not exist or missing read permission!", file=sys.stderr)
    sys.exit(Const.FILE_DONT_EXIST_ERROR)
except elementTree.ParseError:
    print("Not well-formed XML file!", file=sys.stderr)
    sys.exit(Const.INVALID_XML_ERROR)
except (InvalidXmlException, InvalidCodeException, BadOperandTypeException,
        NonExistingVarException, InvalidFrameException, MissingValueException,
        BadValueException, InvalidStringOperationException) as e:
    print(e.msg, file=sys.stderr)
    sys.exit(e.error)
sys.exit(Const.ERROR_OK)
