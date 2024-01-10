import os, sys, shutil
import threading
import logging
from Cheetah.Template import Template

from skeletonz.managers import RSSManager 
from skeletonz.managers import MailManager 
from skeletonz.managers import FormatManager 

from skeletonz.user_plugins import PluginManager

from amilib import template
from amilib import amiformat

import urllib

from amilib.amiweb import amiweb
from amilib.amiweb import configurator
from amilib.amiweb import amidb

#Set configurator's - important they are in top
skeletonz_configurator = configurator.Configurator("dynamic_config_files/skeletonz")
plugin_configurator = configurator.Configurator("dynamic_config_files/plugins")


#--- API calls ----------------------------------------------
general_config = None
def getConfig():
    return general_config

plugins = None
def getPluginManager():
    return plugins

def getPlugins():
    return getPluginManager().getPlugins()

root_controller = None
def getRootController():
    return root_controller

rss_manager = RSSManager()
def getRSSManager():
   return rss_manager

mail_manager = MailManager()
def getMailManager():
   return mail_manager

format_manager = FormatManager()
def getFormatManager():
  return format_manager

def getCurrentPage():
    amiweb.session().SessFact['current_page_id'] = getMainPageId()
    return int(amiweb.session().SessFact['current_page_id'])

def getMainPageId():
    if hasattr(getConfig(), 'START_PAGE_ID'):
        return getConfig().START_PAGE_ID
    return 1


#--- Database ----------------------------------------------
db_pool = None
def configureDatabase():
    global db_pool

    pool = amidb.ConnectionPool
    if getattr(general_config, 'USE_POOL', None) == False:
        pool = amidb.FreshPool
        print "Database connection pooling is disabled."

    dbinfo = amidb.AmiDBInfo(general_config.DB_USER,
                         general_config.DB_PASSWORD,
                         general_config.DB_DATABASE,
                         general_config.DB_HOST,
                         general_config.TABLE_PREFIX)
    db_pool = pool(dbinfo)

def getDBPool():
    global db_pool
    if not db_pool:
        configureDatabase()
    return db_pool

def getDBConnection():
    """
    Returns amiweb.db, useful if you want to communicate to the db outside Skeletonz
    """
    configureDatabase()
    amiweb.setUpConnection()
    return amiweb.db


#--- Internal imports ----------------------------------------------
from mylib import sk_middleware
from mylib.url_mapper import mapToName

from model import db_structure
from model import CMSModel
from mylib.amicache import AmiCache


#--- Create root structure ----------------------------------------------
def createApplication():
    import Site
    import Admin as Admin
    from modules.template import AdminTemplate, SiteEditTemplate

    #Clean up the old templates
    shutil.rmtree("dynamic_dirs/generated")
    os.mkdir("dynamic_dirs/generated")

    cur_tmpl = general_config.CURRENT_TEMPLATE

    site_template = cur_tmpl.SiteTemplate()
    site_template.markChildren()

    site_edit_template = SiteEditTemplate(cur_tmpl.SiteTemplate)
    admin_template = AdminTemplate()

    def getRootObject():
        #Site mappings
        root_object = Site.Site(site_template)
        root_object.siteedit = Site.SiteEdit(site_edit_template)
        root_object.users = Site.Users
        root_object.pages = Site.Pages
        root_object.menu_edit = Site.MenuEdit( site_edit_template.getMenu() )

        #Admin mappings
        root_object.admin = Admin.Main(admin_template)
        root_object.admin.UserManager = Admin.UserManager(admin_template)
        root_object.admin.GroupManager = Admin.GroupManager(admin_template)
        root_object.admin.SiteManager = Admin.SiteManager(admin_template)
        root_object.admin.UploadManager = Admin.UploadManager(admin_template)
        root_object.admin.BackupManager = Admin.BackupManager(admin_template)
        root_object.admin.ServerManager = Admin.ServerManager(admin_template)
        root_object.admin.PluginManager = Admin.PluginManager(admin_template)
        root_object.admin.TemplateManager = Admin.TemplateManager(admin_template)

        #Generic plugin - so plugins can register themselfs
        class Plugin:
            pass
        root_object.plugin = Plugin()
        return root_object
    return getRootObject()


def setTemplateStaticPaths(wsgi_app, old_template_name=None):
    cur_dir = os.getcwd()

    wsgi_app.removeStaticPath('/static_tmpl/')

    path_template = "%s/templates/%s/static/" % (cur_dir, general_config.CURRENT_TEMPLATE.NAME)
    wsgi_app.static_handler.addStaticPath('/static_tmpl/', path_template)


#--- Setup a WSGI app ----------------------------------------------
def setupWSGIApp():
    amiformat.amiformat.DOMAIN = general_config.BASE_URL

    amiweb.db_pool = getDBPool()
    amiweb.setUpConnection()

    #Set up template base namespace and mode
    template.MODE = general_config.MODE
    template.BASE_NS = {'pathname2url': urllib.pathname2url, 'BASE_URL': getConfig().BASE_URL}

    #Initialize plugins
    global plugins
    plugins = PluginManager()

    root_object = createApplication()

    #Let plugins add their own table creation
    for plugin in getPlugins():
        try:
            plugin['module'].createStructure()
        except NotImplementedError:
            pass

    ##
    # AmiWeb configuration
    #
    cur_dir = os.getcwd()

    #Session
    session_config = {'type': 'File',
                      'storage_path': '%s/dynamic_dirs/sessions' % cur_dir,
                      'cookie_name': general_config.COOKIE_NAME}

    #Static paths
    static_paths = {
        '/static_core/': "%s/skeletonz/static/" % cur_dir,
        '/uploads/': "%s/dynamic_dirs/uploads" % cur_dir,
        '/generated/': "%s/dynamic_dirs/generated" % cur_dir,
        '/rss/': "%s/dynamic_dirs/rss" % cur_dir,
        '/static/': "%s/static" % cur_dir
    }

    amiweb.setUpFileLogging("%s/dynamic_dirs/logs/server_log" % cur_dir)
    config = {
        'mode': general_config.MODE,
        'log_paths': True,
        'session': session_config,
        'static_paths': static_paths,
        'db_pool': getDBPool()
    }

    print "[server] setupWSGIApp: before amiweb.RootController()<br>"
    rc = amiweb.RootController(root_object, config)

    #Set global
    global root_controller
    root_controller = rc

    #Init the migrator
    db_structure.initDatabaseIfNeeded()

    #Let plugins append themselves
    for plugin in getPlugins():
        try:
            plugin['module'].addToController(rc)
        except NotImplementedError:
            pass

    #Cache support
    print "[server] setupWSGIApp: no Cache support ,...so no threads<br>"
    #if general_config.BUILD_CACHE_ON_START:
        #t = threading.Thread(target=AmiCache.cacheAllPages)
        #t.start()
        #print "Cache building thread started."

    #Users template
    setTemplateStaticPaths(rc)

    rc.dispatcher.setErrorHandler(mapToName)

    wsgi_app = sk_middleware.SpellChecker(rc.wsgi_app)
    amiweb.releaseConnection()
    print "[server] setupWSGIApp:about to return wsgi_app<br>"
    
    return wsgi_app


#--- Server starter ----------------------------------------------
def startServer():
    wsgi_app = setupWSGIApp()
    print "[server] startServer: starting app?<br>"
    #wsgi_app = validate.validator(wsgi_app)
    logging.basicConfig(level=logging.DEBUG)

    amiweb.startServer(wsgi_app, general_config.PORT)
