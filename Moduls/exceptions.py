class InvalidXmlException(Exception):
    def __init__(self, msg):
        self.msg = msg
        self.error = 32


class WritePermissionError(PermissionError):
    def __init__(self, msg):
        self.msg = msg
        self.error = 12


class InvalidCodeException(Exception):
    # 52
    def __init__(self, msg):
        self.msg = msg
        self.error = 52


class BadOperandTypeException(Exception):
    # 53
    def __init__(self, msg):
        self.msg = msg
        self.error = 53


class NonExistingVarException(Exception):
    # 54
    def __init__(self, msg):
        self.msg = msg
        self.error = 54


class InvalidFrameException(Exception):
    # 55
    def __init__(self, msg):
        self.msg = msg
        self.error = 55


class MissingValueException(Exception):
    # 56
    def __init__(self, msg):
        self.msg = msg
        self.error = 56


class BadValueException(Exception):
    # 57
    def __init__(self, msg):
        self.msg = msg
        self.error = 57


class InvalidStringOperationException(Exception):
    # 58
    def __init__(self, msg):
        self.msg = msg
        self.error = 58
