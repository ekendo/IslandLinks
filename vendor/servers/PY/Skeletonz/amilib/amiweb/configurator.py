import re
import os
try:
    import cPickle as pickle
except ImportError:
    import pickle
import types
re_sec = re.compile("^\[.+\]")
re_val = re.compile(".+: .*")

class Configurator:
    def __init__(self, file="dynamic_configuration"):
        self.sections = {}
        self.file = file
    
    def _getSection(self, section):
        sec = self.sections.get(section, None)
        if sec == None:
            self.sections[section] = {}
            return self.sections[section]
        else:
            return sec
      
    def set(self, section, key, value):
        sec = self._getSection(section)
        if type(value) in (types.StringType,types.IntType):
            sec[key] = value
        else:
            sec[key] = "[binary]%s[/binary]" % pickle.dumps(value,protocol=pickle.HIGHEST_PROTOCOL)
            sec[key] = sec[key].replace('\n', '@!RETURN!@')
        self._saveToFile()
    
    def get(self, section, key):
        self._loadFromFile()
        sec = self._getSection(section)
        value = sec.get(key, None)
        if value and (value[:8],value[-9:])==("[binary]","[/binary]"):
            value = pickle.loads(value[8:-9].replace('@!RETURN!@','\n'))
        return value
    
    def deleteAll(self):
        self.sections = {}
        os.remove(self.file)
    
    def invalidate(self):
        self._loadFromFile()
    
    def _saveToFile(self):
        fp = open(self.file, "w")
        keys = self.sections.keys()
        keys.sort()
        for sec in keys:
            fp.write("[%s]\n" % sec)
            kws = self.sections[sec]
            keys = kws.keys()
            keys.sort()
            for k in keys:
                fp.write("%s: %s\n" % (k, kws[k]))
            fp.write("\n")
          
    def _loadFromFile(self):
        loaded = {}
    
        try:
            fp = open(self.file, "r")
        except IOError:
            return 
        current_sec = None
        for l in fp.readlines():
            if re.match(re_sec, l):
                sec = l.replace("[", "", 1).replace("]", "", 1).rstrip()
                loaded[sec] = {}
                current_sec = sec
        
            if re.match(re_val, l):
                k, v = l.split(": ")
                loaded[current_sec][k] = v.rstrip()
        self.sections = loaded
