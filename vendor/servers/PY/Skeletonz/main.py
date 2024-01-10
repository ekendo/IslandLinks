#! /usr/bin/python
import os, sys
file_dir = os.path.abspath(os.path.dirname(__file__))
os.chdir(file_dir)
sys.path.insert(0, os.path.abspath("amilib"))



import traceback
import glob
import logging
import logging.handlers
import urllib
from amilib.amiweb.wsgiserver import HTTPConnection
from amilib.amiweb.wsgiserver import HTTPRequest




adminPage = 0

try:
    qs = '<!--'+os.environ["QUERY_STRING"]+'//-->'
    print "Content-type:text/html\n\n"
except:
    print("Location:http://thisis.mywebsite.com/Skeletonz/main.py/site/main?page_id=1")
    print # to end the CGI response headers.

'''if (os.environ[ "QUERY_STRING"] is None):
    print("Location:http://thisis.mywebsite.com/cgi-bin/Skeletonz/main.py/site/main?page_id=1")
'''
print "<!--Debug"
        
try:
    #debug headers,...
    '''
    for param in os.environ.keys():
        print "<br><b>%20s</b>: %s<\br>" % (param, os.environ[param])
        if param == "QUERY_STRING":
              print "found param var<br>"
    '''
    #print "<br>"
    import general_config #usual error point
    #print "<p>after config</p>"
    from skeletonz import server
    #print "<p>[main]before starting server</p>"
    server.general_config = general_config
    server.startServer()
    #print "<p>[main]server starting,...</p>"
    #build response
    con = HTTPConnection(os.environ, server)
    #con.communicate()
    from skeletonz.server import getConfig
    rc = server.getRootController() 
    #print "<p>[main]after controller assignment</p>"
    status = '200 OK'
    response_headers = [('Content-type','text/plain')]
    req = HTTPRequest(con)
    #req.start_response(status, response_headers)
    #print "<p>[main] after start_response</p>"
    rc.__call__(os.environ, req.start_response)
    #print "<p>[main] after root controoller call</p>"
    #end debug headers
    

except Exception, err:
    print traceback.format_exc()
    
print "End Debug//-->"

#sys.exit (1)


