import Cookie
import urllib
import types
import datetime

import amidb
from amilib import autoreload
from amilib import json

#Other project imports
import wsgiserver
import traceback
import os
import threading
import logging
from logging import handlers

from configurator import Configurator

import static

#paste imports
from paste.request import parse_formvars
from paste.session import SessionMiddleware, FileSession
from paste.util.threadinglocal import local
from paste import translogger
from paste import gzipper

from error_handler import returnHttpError
errorHandler = returnHttpError


##
# Exceptions
#
class NotFound(Exception): pass

class HTTPError(Exception): pass

class HTTPNotFound(HTTPError):
    code = 404
    title = 'Not Found'
    explanation = ('The resource could not be found.')

class HTTPForbidden(HTTPError):
    code = 403
    title = 'Forbidden'
    explanation = ('Access was denied to this resource.')

class HTTPBadRequest(HTTPError):
    code = 400
    title = 'Bad Request'
    explanation = ('The server could not comply with the request since\r\n'
                   'it is either malformed or otherwise incorrect.\r\n')

class HTTPFound(HTTPError):
    code = 302
    title = 'Found'
    explanation = 'The resource was found at'

class HTTPInternalServerError(HTTPError):
    code = 500
    title = '[amiweb]HTTPInternalServerError:Internal Server Error'
    explanation = ('[amiweb]HTTPInternalServerError:The server has either erred or is incapable of performing\r\n'
                   'the requested operation.\r\n')

class AppError(HTTPError):
    code = 500
    title = '[amiweb]HTTPError:Internal Server Error'
    explanation = "Application error:\r\n"

    def __init__(self, expl):
        self.explanation = "[amiweb]AppError:%s%s" % (self.explanation, expl)


##
# Stuff for export (should be accessed through amiweb.)
#
db_pool = None
config = {}
thread_data = local()

def db():
    try:
        #print "[amiweb]DB:before db connnection assignment<br>"
        db_connection = thread_data.db_connection
        #print "[amiweb]DBafter db connnection assignment<br>"
        
    except AttributeError:
        thread_data.db_connection = db_pool.getConnection()
    return thread_data.db_connection

def request():
    return thread_data.request

def response():
    return thread_data.response

dummy = {}
def session():
    try:
        #print "[amiweb] session(): thread Data has value?<"
        #print thread_data.session()
        #print "><br>"
        return thread_data.session
    except AttributeError:
        return dummy

#--- Decorators ----------------------------------------------
def expose(func):
    func.exposed = True
    return func

def staticHandler(func):
    func.static_handler = True
    return func

def customHandler(func):
    func.custom_handler = True
    return func

def contentType(type):
    def decorate(f):
        f.custom_content_type = type
        return f
    return decorate


##
# Server stuff
#
def startServer(wsgi_app, port=8080, numthreads=10):
    global config
    def start():
    	#print "[amiweb]startServer_start_: before server set<br>"
        server = wsgiserver.CherryPyWSGIServer(('', port), wsgi_app, numthreads=numthreads, request_queue_size=15, timeout=10)
        try:
            #print "[amiweb]startServer_start_:Server started on port %s<br>" % port
            server.start()
            #print "[amiweb]startServer_start_:after server.start w/ CherryPyBusiness<br>"
        except KeyboardInterrupt:
            #print "[amiweb]startServer_start_:Server stopped<br>"
            server.stop()
	#print "[amiweb]startServer_start_: after server set<br>"
        

    if config.get('mode', 'testing') == "testing":
        autoreload.main(start)
    else:
        start()


##
# Database stuff
#
def setUpConnection():
    pass

def releaseConnection():
    pass


##
# Configure the logger
#
translogger.TransLogger.format = ('%(REMOTE_ADDR)s - %(REMOTE_USER)s [%(time)s] '
              '"%(REQUEST_METHOD)s %(REQUEST_URI)s %(HTTP_VERSION)s" '
              '%(status)s %(bytes)s')

def setUpFileLogging(path_to_file):
    logger = logging.getLogger("amiweb")

    #10485760 = 10mb
    fh = handlers.RotatingFileHandler(path_to_file, maxBytes=10485760, backupCount=5)
    logger.addHandler(fh)


##
# WSGI part
#
class RootController:

    def __init__(self, root_obj, cfg):
        global thread_data
        #Session config
        if cfg.has_key('session'):
            ses_cfg = cfg['session']
            ses_kw = {'cookie_name': ses_cfg.get('cookie_name', 'AmiWeb')}

            if ses_cfg.get('type') == 'File':
                ses_kw['session_class'] = FileSession
                ses_kw['session_file_path'] = ses_cfg.get('storage_path')
            else:
                ses_kw['session_class'] = None
        else:
            ses_kw = {}

        ses_kw['session_expiration'] = 60*336 #14 days
        ses_kw['expiration'] = 60*384
        
        SessionMiddleware.cleanup_cycle = datetime.timedelta(seconds=360*60) #6 hours

        #Database configuration
        if cfg.has_key('db_pool'):
            global db_pool
            self.use_db = True
            db_pool = cfg['db_pool']
            #print "[amiweb]RootController:cfg DOES have key db pool<br>"

        else:
            self.use_db = False
            #print "[amiweb]RootController:cfg does NOT have key db pool<br>"

        self.dispatcher = Dispatcher(root_obj, self)

        wsgi_app = self.dispatcher
	thread_data.session = SessionMiddleware(wsgi_app, **ses_kw)
        #wsgi_app = SessionMiddleware(wsgi_app, **ses_kw)
        wsgi_app = thread_data.session
        
        self.static_handler = StaticMiddleware(wsgi_app, self.dispatcher)
        #wsgi_app = self.static_handler
        
        if cfg.has_key('static_paths'):
            static_paths = cfg['static_paths']
            for k in static_paths:
                self.addStaticPath(k, static_paths[k])


        #if cfg.get('log_paths', True):
        #    wsgi_app = translogger.TransLogger(wsgi_app, logger_name="amiweb")


        global config
        config = cfg

        self.wsgi_app = wsgi_app
        self.root_obj = root_obj

    def addStaticPath(self, url_prefix, os_path):
        """
        @param url_prefix - The URL that should be served
        @param os_path - The absolute path on the O/S. Should not end on a /.
        """
        self.static_handler.addStaticPath(url_prefix, os_path)

    def removeStaticPath(self, url_prefix):
        self.static_handler.removeStaticPath(url_prefix)

    def __call__(self, environ, start_response):
        #print '[amiweb]Rootcontroller_call_:</br>'
        return self.wsgi_app.__call__(environ, start_response)


##
# Static handler of files
#
class StaticMiddleware:

    def __init__(self, wsgi_app, dispatcher):
        self.static_handling = {}
        self.wsgi_app = wsgi_app
        self.dispatcher = dispatcher
        self.path_favicon = "%s/amilib/amiweb/favicon.ico" % os.getcwd()

    def addStaticPath(self, url_prefix, os_path):
        cling = static.Cling(os_path)
        def _full_path(path_info):
            path_info = path_info.replace(url_prefix, '/', 1)
            path = "%s%s" % (os_path, path_info)
	    path = os.path.abspath(path)
	    return path
        cling._full_path = _full_path
        self.static_handling[url_prefix] = (os_path, cling)

    def removeStaticPath(self, url_prefix):
        if self.static_handling.has_key(url_prefix):
            del self.static_handling[url_prefix]

    def _handleStatic(self, environ, start_response):
        #path = environ['PATH_INFO']
        path = "/home/users/web/b453/hy.ekendodreamof/cgi-bin/Skeletonz/"
        path = path.replace("//", "/")

        if path == "/favicon.ico":
            return self.dispatcher._serveStaticFile(self.path_favicon, environ, start_response)

        for url_prefix in self.static_handling:
            os_path, cling = self.static_handling[url_prefix]
            if path.find(url_prefix) == 0:
                return cling(environ, start_response)

    def __call__(self, environ, start_response):
        static_resp = self._handleStatic(environ, start_response)
        if static_resp:
            return static_resp
        else:
            #print '[amiweb]StaticMiddleware:call to not handle Static<br>'
            return self.wsgi_app(environ, start_response)
             


##
# Dispatcher
#
class Dispatcher:

    def __init__(self, root_obj, root_controller):
        self.root_obj = root_obj
        self.use_db = root_controller.use_db
        self.root_controller = root_controller
        #print '[amiweb] dispatcher initd'
        self.path_memo = {}

    def expirePathCache(self):
        self.path_memo = {}

    def setErrorHandler(self, fun):
        """
        Is called if the node isn't found
        """
        #print "[amiweb] Dispatcher setErrorHandler: node wasnlt found<br>"
        self.error_handler = fun


    def setFavIconPath(self, full_path):
        """
        Set the full path to the fav icon
        """
        self.path_favicon = full_path

    def _serveStaticFile(self, full_path, environ, start_response):
        """
        AmiWeb can serve a static file.
        This is done if one uses the staticHandler decorator.
        """
        cling = static.Cling('')
        file_like = cling._file_like(full_path)
        content_type = cling._guess_type(full_path)
        start_response("200 OK", [('Content-Type', content_type)])
        return cling._body(full_path, environ, file_like)

    def _getNode(self, path_info):
        #print "[amiweb]in getNode<br>"
        if self.path_memo.get(path_info, None):
            node = self.path_memo[path_info]
        else:
            #print "[amiweb]_getNode:path momo ! None<br>"
            #issue in mapPathToObject causes 500 error,...needs SessionObject?
            #node  = None
            node = self._mapPathToObject(path_info, self.root_obj)
            self.path_memo[path_info] = node
        #print "[amiweb]bout to return from getNode<br>"
        return node

    def __call__(self, environ, start_response):
        global thread_data, db_pool
        #print '<br>[amiweb]dispatcher call<br>'
        thread_data.db_connection = db_pool.getConnection()

        try:
            #print "[amiweb]Dispatcher_call_:<"
            #PATH_INFO is null
            #environ["PATH_INFO"] = "/home/users/web/b453/hy.ekendodreamof/cgi-bin/Skeletonz"
            environ["PATH_INFO"] = "/hermes/waloraweb079/b453/hy.ekendodreamof/cgi-bin/Skeletonz"
            #print environ["PATH_TRANSLATED"]
            #print "><br>"
            path_info = environ["PATH_TRANSLATED"]
            #path_info = "/hermes/waloraweb079/b453/hy.ekendodreamof/cgi-bin/Skeletonz"
            #print "[amiweb]Dispatcher_call_:after setting pathinfo manually<br>"

            body = ""

            thread_data.request = environ
            thread_data.response = {'headers': []}
            thread_data.response['headers'].append(('Content-Type', '%s; charset=%s' % ('text/html', 'utf-8')))

            #print "[amiweb]Dispatcher:before Session support<br>"

            
            #Session support tofix lata
            def lazy():
                return environ['paste.session.factory']()
            #thread_data.session = lazy
   
            #print "[amiweb]Dispatcher_call_:after lazy stuff<"
            #print thread_data.session
            #print "><br>"
	    node = None

            # First step: Try to find the node correct node from path_info
            try:
                node = self._getNode(path_info)
                #node = None
                #print "[amiweb]Dispatcher:after _getNode<br>"
            except NotFound:
                try:
                    if getattr(self, "error_handler", None):
                        val = self.error_handler(self.root_controller, environ, start_response)
                    else:
                        raise HTTPNotFound()
                        #print "[amiweb]dispatcher_call_:normally HTTPNotFound<br>"
                except HTTPError, (e):
                    traceback.print_exc()
                    print "[amiweb]dispatcher_call_:'%s' raised HTTP error '%i %s'<br>" % (path_info, e.code, e.title)
                    val = errorHandler(e, environ, start_response)
                #except:
                    #traceback.print_exc()
                    #e = HTTPInternalServerError()
                    #print "[amiweb]dispatcher_call_1:'%s' raised internal HTTP error '%i %s'<br>" % (path_info, e.code, e.title)
                    #val = errorHandler(e, environ, start_response)
                #return val
            
            #print "[amiweb] Dispatcher_call_:after first step node is=<"
            #print node
            #print "><br>"

            # Second step: Pass arguments to the found node
            
            try:
                #print "[amiweb]dispatcher_call_:node<"
                #print node
                #print "><br>"
                
                """
                if node[0] != None:
                    #If it's an static handler, then handle it
                    if getattr(node[0], "static_handler", False):
                        full_path = node[0](path_info.split("/")[-1])
                        return self._serveStaticFile(full_path, environ, start_response)

                    try:
                        filter = node[0].im_self.filter()
                        #print "[amiweb]dispatcher_call_:after filter<br>"
                    except AttributeError:
                        pass

                    formvars = parse_formvars(environ) or {}

                    if type(formvars) != types.DictType:
                        formvars = formvars.mixed()

                    body = self.serveNode(path_info, node, formvars)
		    #print "[amiweb]dispatcher_call_:after body = serveNode<br>"
		"""  
            except HTTPError, (e):
                val = errorHandler(e, environ, start_response)
                return val
            except:
                traceback.print_exc()
                e = HTTPInternalServerError()
                print "[amiweb]dispatcher_call_2:'%s' raised internal HTTP error '%i %s'<br>" % (path_info, e.code, e.title)
                #val = errorHandler(e, environ, start_response)
                #return val

	    #print "[amiweb]_call_:after second step, before serve body, body is=<"
	    print body
	    #print "><br>"


            return self.serveBody(body, start_response)
        finally:
            db_pool.releaseConnection(thread_data.db_connection)
            del thread_data.db_connection

    def serveNode(self, path_info, node, formvars):
        content_type = getattr(node[0], 'custom_content_type', None)
        if content_type:
            thread_data.response['headers'] = [('Content-Type', '%s; charset=%s' % ('text/html', 'utf-8'))]

        if getattr(node[0], "custom_handler", False):
            body = node[0](path_info, formvars)
        else:
            body = node[0](**formvars)
        return body

    def serveBody(self, body, start_response):
        global thread_data

	#print "[amiweb] Dispatcher serveBody:<br>"
        #F0rce UTF-8
        headers = [h for h in thread_data.response['headers']]
        start_response('200 OK', headers)

        if type(body) == types.GeneratorType:
            #print "[amiweb] Dispatcher serverBody: before return body<br>"
            return body
        else:
            try:
                body = unicode(body)
                body = body.encode("utf-8")
                #print "[amiweb] Dispatcher serverBody: after body encode<br>"
            except:
                pass
                
            print "[amiweb] Dispatcher serveBody:about to return< End Debug//-->"
            body = body.replace('<br />','&nbsp;')
            print body 
            print "<!-- More Debug ><br>"
            return body

    def _mapPathToObject(self, path, root):
        """For path, return the corresponding exposed callable (or raise NotFound)."""
        # Remove leading and trailing slash
        
        #print "[amiweb]mappin path to obj: w/ root<"
        #print root
        #print "><br>"
        
        tpath = path.strip("/")
        #print tpath
        if not tpath:
            objectPathList = []
        else:
            objectPathList = tpath.split('/')
        
        objectPathList = objectPathList

        #print "[amiweb]_mapPathToObject:after objectPathstuffs<"
        #print self._getObjFromPath(objectPathList + ['index'], root)
        #print "><br>"

        #Try with index
        foundIt = False
        candidate = self._getObjFromPath(objectPathList + ['index'], root)

        if callable(candidate) and getattr(candidate, 'exposed', False):
            foundIt = True
            #print "[amiweb]mappin path to obj: foundIt!"
        else:
            #print "[amiweb]mappin path to obj: !foundIt"
            candidate = self._getObjFromPath(objectPathList, root)
            if callable(candidate) and getattr(candidate, 'exposed', False):
                foundIt = True
            else:
                #Try to remove one - for static_handler
                candidate = self._getObjFromPath(objectPathList[:-1], root)
                if callable(candidate) and getattr(candidate, 'exposed', False):
                    if getattr(candidate, 'static_handler', False) or getattr(candidate, 'custom_handler', False):
                        foundIt = True

        # Check results of traversal
        if not foundIt:
            raise NotFound()

        return candidate, objectPathList

    def _getObjFromPath(self, objPathList, root):
    
        """For a given objectPathList, return the object (or None).
        objPathList should be a list of the form: ['root', 'a', 'b', 'index'].
        """
        
        #print "[amiweb]_getObjFromPath:objPathList:<"
        #print objPathList
        #print "><br>"
        
        for objname in objPathList:
            # maps virtual filenames to Python identifiers (substitutes '.' for '_')
            #print "[amiweb]_getObjFromPath:"
            #print objname
            #print ":after objname<br>"
            objname = objname.replace('.', '_')
            root = getattr(root, objname, None)
            #root = objPathList
            #print "[amiweb]_getObjFromPath:"
            #print root
            #print ":after root<br>"
            
            if root is None:
                return None
            #else:
            #    print "[amiweb]returning the root with value<br>"
                
        return root
