import re

#--- Exceptions ----------------------------------------------
class ParseError(Exception):
    """Rasied when a parse error is encountered."""
    pass

class EOT(Exception):
    """Raised on end of tokens."""
    pass


#--- Simple token ----------------------------------------------
class Token:

    def __init__(self, type, value=None):
        self.type = type
        self.value = value

    def __repr__(self):
        return '[%s: %s]' % (self.type, self.value)


#--- Base lexer ----------------------------------------------
class Lexer:

    def __init__(self, tokens):
        """
        If data_text is set, then text wont be put inside <p>...</p>
        """
        self.local_reg_exp = {}
        for tu in tokens:
            self.local_reg_exp[tu[0]] = re.compile(tu[1], re.VERBOSE)

        self.global_reg_exp = re.compile(self._buildRegular(tokens),
                re.VERBOSE)

    def tokenize(self, text):
        for m in self.global_reg_exp.finditer(text):
            group = m.lastgroup
            text = m.group(group)
            fn = getattr(self, '%s' % group)
            fn(self.local_reg_exp[group].match(text))

    def _buildRegular(self, tokens):
        reg_exp = []
        for tu in tokens:
            reg_exp.append(self._makeRE(tu[0], tu[1]))
        return '|\n'.join(reg_exp)

    def _makeRE(self, name, reg):
        return '(?P<%s>%s)' % (name, reg)


#--- Base parser ----------------------------------------------
class Parser:

    def __init__(self, tokens):
        self.index = -1
        self.tokens = tokens
        self.current_t = None
        self.output = []
        self.line_pos = 1

    def parseError(self, text):
        raise ParseError('\nA parse error was encountered on line %i.\n%s.' % (self.line_pos, text))

    def skipToken(self, tok):
        if self.hasNextToken() and self.getNextToken().type == tok:
            self.nextToken()


    def nextToken(self):
        self.index += 1
        if self.index <= len(self.tokens)-1:
            self.ct = self.tokens[self.index]
        else:
            self.ct = None
            raise EOT()

    def checkNextToken(self, tok):
        if self.hasNextToken():
            return self.tokens[self.index+1].type == tok
        else:
            return False

    def getNextToken(self):
        if self.hasNextToken():
            return self.tokens[self.index+1]
        else:
            return None

    def hasNextToken(self):
        return self.index < len(self.tokens)-1

    def getOutput(self):
        return ''.join(self.output)
