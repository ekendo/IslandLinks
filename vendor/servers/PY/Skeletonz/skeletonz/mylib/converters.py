import re

def makeLinkAble(input):
    s = input
    if s is None:
        return None
    for c in (" ", ".", "_", "?", "!", "'", '"'):
        s = s.replace(c, "_")
    return s

def appendSiteEdit(logged_in):
    if logged_in:
        return 'siteedit/'
    return ''

def pageNameSpaces(name):
    def subber(mobj):
        if mobj.group(0) == r'\_':
            return '_'
        else:
            return ' '
    return re.sub(r'(\\)?_', subber, name)
