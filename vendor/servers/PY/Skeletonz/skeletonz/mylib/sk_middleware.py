import sys, os
import urllib

import httplib
from skeletonz.server import getConfig
#from amilib.amiweb.paste import httpexceptions

class AdminLoginCheck:
    def __init__(self, wsgi_app):
        self.wsgi_app = wsgi_app

    def __call__(self, environ, start_response):
        path = environ['PATH_INFO']
        if path.find("/admin/") == 0:
            session = environ['beaker.session']
            if session.get('admin_logged_in', False) == False:
                raise httpexceptions.HTTPFound("%s/login/" % (getConfig().BASE_URL))
        return self.wsgi_app(environ, start_response)

class SpellChecker:

    def __init__(self, wsgi_app):
        self.wsgi_app = wsgi_app

    def __call__(self, environ, start_response):
        path = environ['PATH_INFO']
        if path.find("/spellchecker/") == 0:
            r_text = ""
            lang = environ['QUERY_STRING'].replace("lang=", "")

            if len(lang) != 2:
                lang = "en"

            data_len = int(environ.get('CONTENT_LENGTH', 0))
            if data_len == 0:
                data_len = int(environ.get('HTTP_CONTENT_LENGTH', 0))

            data = environ.get("wsgi.input").read(data_len)
            con = httplib.HTTPSConnection("www.google.com")
            con.request("POST", "/tbproxy/spell?lang=%s" % lang, data)
            response = con.getresponse()
            r_text = response.read()
            con.close()

            start_response("200 OK", [('Content-Type', 'text/html')])
            return [r_text]
        return self.wsgi_app(environ, start_response)
