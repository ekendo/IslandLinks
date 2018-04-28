from amilib.Cheetah.Template import Template
from amilib.Cheetah.Compiler import Compiler
from amilib.Cheetah.NameMapper import NotFound

import imp
import os, sys
import types

import urllib

#Should be changed from outside
MODE = "deployment"
BASE_NS = {}

TEMPLATE_MEMO = {}
def compile_template(path, is_string=False, base=None):
    """
    path: The path of the template
    is_string: If the path is actually a Cheetah string that we want to compile
    base: If another tmpl file should be used a base that provides functions
    """
    
    #print "[template] compile_template:<br>"
    path = "/home/users/web/b453/hy.ekendodreamof/cgi-bin/Skeletonz/"+path
    
    #skipping for now
    #if not is_string:
    #    last_modified = os.stat(path).st_mtime

    #print "[template] compile_template:after is_string<br>"
    

    
    if not is_string and TEMPLATE_MEMO.has_key(path) and last_modified <= TEMPLATE_MEMO[path][1] and MODE == "deployment":
    	#print "[template] compile_template:in the if<br>"
        return TEMPLATE_MEMO[path][0]
    else:
        #print "[template] compile_template:in the else<br>"
        
        text = []
        
        #print "[template] compile_template:after text init<"
        #print path
        #print "><br>"
        
        #is_string is None 
        #if is_string:
        #	text.append(path)
        #else:
        text.append(open(path).read())
	
	#print "[template] compile_template:after is_string stmt<br>"
        
	# base is None
        if base:
            text.insert(0, open(base).read())

	#print "[template] compile_template: right before Compiler<"
	#print is_string
	#print "><br>"
        
        
        try:
            c = Compiler(source="".join(text), mainClassName='GenTemplate')
            ns = {}
            #print "[template] compile_template: stuff worked<br>"
        except Error:
            print "[template] compile_template: stuff broke<br>"
        
        
        try:
            exec str(c) in ns
            tempclass = ns.get("GenTemplate", None)
            #print "[template] compile_template: templclass setting success<br>"
        except Error:
            print "[template] compile_template: templclass setting failure<br>"
        
        #if not is_string:
        #    TEMPLATE_MEMO[path] = (tempclass, last_modified)
        
        #print "[template] compile_template: about to return temp class<br>"
        return tempclass
      
    #print "[template] compile_template:no temp class for now<br>"
    

def render(path, ns={}, is_string=False, base=None):
    #print "[template] render:path<"
    if type(ns) == types.DictType:
        ns.update(BASE_NS)
        ns = [ns]
    else:
        ns = BASE_NS

    #print path
    #print "><br>"
    
    #print "[template] render:ns<"
    #print ns
    #print "><br>"
    
    
    try:
        #t = "%s" % compile_template(path, is_string, base)(searchList=ns)
        t = compile_template(path, is_string, base)(searchList=ns)
        #print "[template] render: after the t set to the compiled template<br>"
        return t
    except NotFound, e:
        msg = "%s in file '%s'" % (e, path)
        #print "[template] render: exception after the t business<br>"
        #print "\nTEMPLATE ERROR\n%s\n%s\n" % ("="*22, msg)
        return msg
