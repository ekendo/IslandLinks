import htmlentitydefs
import re, string
import types

partial = lambda func, *args, **kw:\
              lambda *p, **n:\
                  func(*args + p, **dict(kw.items() + n.items()))

def flatten(x):
    result = []
    for el in x:
        #if isinstance(el, (list, tuple)):
        if hasattr(el, "__iter__"):
            result.extend(flatten(el))
        else:
            result.append(el)
    return result

class Singleton(object):
    def __new__(cls, *args, **kwds):
        it = cls.__dict__.get("__it__")
        if it is not None:
            return it
        cls.__it__ = it = object.__new__(cls)
        it.init(*args, **kwds)
        return it

    def init(self, *args, **kwds):
        pass

import re
RND_MATCHER = re.compile(r'%\[(?P<name>[A-Za-z0-9_|.]*)\]')
def RND(tmpl, ns):
    def convert(mo):
        sp = mo.group('name').split('|')
        name = sp[0]
        filters = sp[1:]
        if ns.has_key(name):
            cnt = ns[name]
            for f in filters:
                cnt = ns[f](cnt)
        else:
            cnt = mo.group(0)
        return str(cnt)
    return RND_MATCHER.sub(convert, tmpl)

def has_keys(dict, *keys):
    for key in keys:
        if not dict.has_key(key):
            return False
    return True

def isString(str):
    return type(str) in [types.StringType, types.UnicodeType]

class AttrDict(dict):

    def __init__(self, *args, **kwargs):
        dict.__init__(self, *args, **kwargs)
        self.__dict__ = self
