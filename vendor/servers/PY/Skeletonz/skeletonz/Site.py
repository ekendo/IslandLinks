import re
import types
import cgi

from amilib import json
from amilib.useful import Singleton
from server import getConfig, getPlugins, getRootController, getPluginManager, getFormatManager, getCurrentPage, getMainPageId

BASE_URL = getConfig().BASE_URL
CHECK_LOGIN = getConfig().CHECK_LOGIN
ADMIN_USERNAME = getConfig().ADMIN_USERNAME

from amilib.amiweb import amiweb

import Admin as Admin
from modules.template import PluginTemplate, AdminTemplate, initTemplate
from modules import sections

import model.CMSModel as Model
import model.UserModel as UserModel
from model import visit

from mylib.amicache import AmiCache
from amilib.template import render
from mylib import url_mapper

from skeletonz.plugins.upload import model as upload_model
import skeletonz.plugins.wikipages.filter as wiki_filter

from amilib import diff


class Users:

    def __init__(self):
        self.template = PluginTemplate()

    @amiweb.expose
    def checkLoginInfo(self, username, password):
        if UserModel.Users.checkLogin(username, password):
            return "ok"
        else:
            return "wrong"

    @amiweb.expose
    def setLogin(self, username, password):
        login_ok = UserModel.Users.checkLogin(username, password)
        if login_ok:
            session = amiweb.session()
            session['logged_in'] = True
            session['username'] = username
            self.setViewEditMode("on")

            AmiCache.expireEditPages()
            page = Model.Pages.getPageById(getCurrentPage())
            ns = {
              'template': self.template,
              'full_link': page.getFullLink()
            }
            return render("skeletonz/view/whole_sites/login_ok.tmpl", ns)

    @amiweb.expose
    def setLogout(self, id):
        session = amiweb.session()
        try:
            id = int(id)
        except:
            id = getCurrentPage()
        session['logged_in'] = False
        session['username'] = None
        AmiCache.expireEditPages()
        page = Model.Pages.getPageById(id)

        url = "%s/%s" % (BASE_URL, page.getFullLink())
        raise amiweb.HTTPFound(url)

    @amiweb.expose
    def showError(self, page_id):
        ns = {'error_title': 'Not logged in',
              'error_message': 'You are not logged in. Please go back and relogin.',
              'page_id': page_id}
        return render("skeletonz/view/whole_sites/error.tmpl", ns)

    def isLoggedIn(self):
        try:
            return amiweb.session().get('logged_in', False)
        except:
            return False

    def isAdmin(self):
        if not self.isLoggedIn():
            return False
        username = amiweb.session()['username']
        return UserModel.Users.isAdmin(username) or username == ADMIN_USERNAME

    def checkLogin(self, page_id=getMainPageId()):
        if not self.isLoggedIn():
            raise amiweb.HTTPFound("%s/users/showError?page_id=%s" % (BASE_URL, page_id))

    def checkCurrentPagePermissions(self):
        page_id = getCurrentPage()
        if not self.checkPagePremission(page_id):
            raise amiweb.AppError("You don't have permissions for this!")

    def checkAdminPermission(self):
        if not Users.isAdmin():
            raise amiweb.AppError("You don't have admin privilege.")

    def checkPagePremission(self, page_id):
        try:
            session = amiweb.session()
        except:
            return False

        if CHECK_LOGIN == False:
            return True

        try:
            username = session.get('username', None)
        except:
            username = None

        if username == None:
            return False

        #Get page
        page = Model.Pages.getPageById(page_id)
        if page == None:
            return False

        if page.premission_type == "Every user":
            #Every user can edit
            return True

        if username == ADMIN_USERNAME or UserModel.Users.isAdmin(username):
            return True
        elif page.premission_type == "user":
            #Check if it's this user
            if username == page.premission_value:
                return True
        elif page.premission_type == "group":
            #Check if the group is there, if it's not reset it
            if UserModel.Groups.getGroupByName(page.premission_value) == None:
                Pages.resetPremissions(page.id)
                return False

            #Check if the user is in this group
            if UserModel.Groups.isInGroup(page.premission_value, username):
                return True
        return False

    @amiweb.expose
    def showLogin(self):
        page = Model.Pages.getPageById(getCurrentPage())
        ns = {'template': self.template,
              'full_link': page.getFullLink()}
        return render("skeletonz/view/whole_sites/login.tmpl", ns)

    def getCurrentUser(self):
        session = amiweb.session()
        if session.has_key('username'):
            user = amiweb.db().query("select usr.*,grp.name as groupname from c4_user usr left join c4_group grp on grp.id=usr.group_id where usr.username='%s'"%session['username'], as_one=True)
            return user
        else:
            return None

    def getViewEditMode(self):
    	#amiweb.session().get("view_edit_mode", "on")
    	amiweb.session().SessFact['view_edit_mode'] = 'on'
        return amiweb.session().SessFact['view_edit_mode']

    def setViewEditMode(self, value):
        amiweb.session()['view_edit_mode'] = value
        AmiCache.expireEditPages()

Users = Users()


#Decorators to check login and permission
def editPermission(fn):
    def new_fn(*k, **kw):
        Users.checkCurrentPagePermissions()
        return fn(*k, **kw)
    return new_fn

def adminPermission(fn):
    def new_fn(*k, **kw):
        Users.checkAdminPermission()
        return fn(*k, **kw)
    return new_fn


##
# Helper functions
#
def getCurrentInfo(template, page_id, edit_mode):
    """
    page_id can be an object or an int/string
    """
    #print "[Site] getCurrentInfo:<br>"
    is_obj = type(page_id) == types.InstanceType
    page_obj = is_obj and page_id or Model.Pages.getPageById(page_id)

    d = {
      'template': template,
      'logged_in': Users.isLoggedIn(),
      'is_admin': Users.isAdmin(),
      'edit_mode': edit_mode,
      'page_obj': page_obj,
      'view_edit_mode': Users.getViewEditMode(),
      'is_CMS_page': True
    }
    #print "[Site] getCurrentInfo:after d<br>"
    if is_obj:
        d['edit_permission'] = False
        d['is_CMS_page'] = False
        d['page_id'] = page_obj.parent_id
    else:
        d['edit_permission'] = Users.checkPagePremission(page_id)
        d['page_id'] = page_id
    #print "[Site] getCurrentInfo: about to return d<br>"

    return d


def renderView(current_info, template_file):
    """
    The function that renders the whole page.
    Used by both Site and SiteEdit.
    """
    #print current_info['is_CMS_page']
    #print "was CMS page?"

    if current_info['is_CMS_page']:
        amiweb.session().SessFact["current_page_id"] = current_info['page_id']

    #print current_info['page_id']
    #print "was page id?"

    getFormatManager().resetPagePlugins()

    template_obj = current_info['template']
    page_obj = current_info['page_obj']

    if template_obj is None:
        print "[Site]renderView:template deal is None"
    else:
        print "[Site]renderView:template has value"

    #print "[Site]renderView:printing type(template_obj)"
    #print type(template_obj)
    #print "<br>"

    header = template_obj.getHeader()
    #print "<br>[Site]renderView:after GetHeader<br>"
    content = template_obj.getContent()
    #print "<br>[Site]renderView:after GetContent<br>"
    footer = template_obj.getFooter()
    #print "<br>[Site]renderView:after GetFooter<br>"

    #Get page obj
    menu = Model.Menus.getStandardMenu()
    #print "<br>[Site]renderView:after Model.Menus.getStandardMenu<br>"

    if page_obj.hidden == 1 and not current_info['logged_in']:
        return """This page is hidden (isn't published yet). <br /> <a href="%s/">Return to homepage</a>""" % BASE_URL
	
    #print "<br>[Site]renderView:after page_obj.hidden<br>"

    #Set site information
    header.setPage(page_obj)

    #print "<br>[Site]renderView:after setPage(page_obj)<br>"

    root_parent = page_obj.getRootParent()
    current_item = menu.getPageItem(root_parent.id)

    #print "<br>[Site]renderView:after current_item set<br>"

    header.setBodyClass( current_item.getClass() )
    content.setBodyClass( current_item.getClass() )
    
    #print "<br>[Site]renderView:after setBodyClass<br>"    

    content.setPage(page_obj)

    can_edit = current_info['edit_permission']

    #print "<br>[Site]renderView:after can_edit<br>"    

    header.setEditMode(can_edit)
    content.setEditPermission(can_edit)
    content.setEditMode(can_edit)

    menu_tmpl = template_obj.getMenu()

    footer.setPage(page_obj)

    #We want to generate content, before we call plugins
    if current_info['edit_mode']:
        page_obj.generateContentEdit()
    else:
        page_obj.generateContent()

    #Let plugins alter template
    
    #print "<br>[Site]renderView:before plugin for loop <br>" 
    
    #plugins for now
    for plugin in getPlugins():
        plugin = plugin['module']
        #print "<br>[Site]renderView: in plugin for loop <br>"
        try:
            if current_info['edit_mode']:
                plugin.addToSiteEditTemplate(template_obj, False)
            else:
                plugin.addToSiteTemplate(template_obj, False)
        except NotImplementedError:
            pass
    
    
    #print "<br>[Site]renderView:before ns stuffs <br>"

    ns = {'template': template_obj,
          'page_obj': page_obj,
          'edit_mode': current_info['edit_mode']}
          
    
    #print "<br>[Site]renderView:before final render <br>" 
          
    rendered = render("templates/%s/view/%s.tmpl" % (template_obj.getName(), template_file), ns)
    
    #print "<br>[Site]renderView:before final return rendered <br>" 
    
    return rendered


class Site:

    def __init__(self, template):
        #print "[Site] Site _init_:<br>"
        self.template = template
        initTemplate(template, 'addToSiteTemplate')

    def servePage(self, page_id, current_info):
        #print "[Site] Site servePage:<br>"
        if page_id and AmiCache.isCacheUp2Date(page_id):
            #print "[Site] Site servePage: pulling page from session<br>"
            if current_info['is_CMS_page']:
                amiweb.session()["current_page_id"] = page_id
            content = AmiCache.getValue(page_id)
        else:
            #print "[Site] Site servePage: loading & rendering page and putting in session<br>"
            content = renderView(current_info, "site_structure")
            page = Model.Pages.getPageById(page_id)

            #Append sections to this
            content = sections.fillSections(current_info, content, page)

            AmiCache.updateCache(page_id, content)
        return content

    @amiweb.expose
    def index(self, page_id=getMainPageId()):
        if Users.isLoggedIn() and Users.checkPagePremission(page_id):
            page = Model.Pages.getPageById(page_id)
            raise amiweb.HTTPFound("%s/siteedit/%s" % (BASE_URL, page.getFullLink()))
        else:
            print "[Site] Site index: <br>"
            page = Model.Pages.getPageById(page_id)
            #print "[Site] index:page<"
            print page
            #print " ><br>"
            # pageFullLink and BASE_URL have value
            # raise amiweb.HTTPFound("%s/siteedit/%s" % (BASE_URL, page.getFullLink()))
            # raise aiweb.HTTPFound("%s/siteedit/%s" % ("http://ekendotech.com/cgi-bin/Skeletonz/main.py/", page.getFullLink()))

	#print "[Site] index: before currentInfo<br>"
        current_info = getCurrentInfo(self.template, page_id, False)
        #print "[Site] index: after current info<br>"
        return self.servePage(page_id, current_info)


class SiteEdit:

    def filter(self):
        Users.checkLogin()

    def __init__(self, template):
        self.template = template
        initTemplate(template, 'addToSiteEditTemplate')

    def servePage(self, page_id, current_info):
        print "[Site] SiteEdit servePage: servingPage<br>"
        edit_mode = current_info['edit_mode']
        if AmiCache.isCacheUp2Date(page_id, is_edit=edit_mode):
            if current_info['is_CMS_page']:
                amiweb.session()["current_page_id"] = page_id
            content = AmiCache.getValue(page_id, is_edit=edit_mode)
        else:
            content = renderView(current_info, "site_structure")

            AmiCache.updateCache(page_id, content, is_edit=edit_mode)
        page = Model.Pages.getPageById(page_id)

        #Append dynamic sections to this
        content = sections.fillSections(current_info, content, page)
        return content

    @amiweb.expose
    def index(self, page_id=getMainPageId()):
        print "[Site] SiteEdit index: <br>"
        can_edit = Users.checkPagePremission(page_id)
        if not can_edit and not Users.isLoggedIn():
            raise amiweb.HTTPFound("%s/?page_id=%s" % (BASE_URL, page_id))

        if can_edit:
            edit_mode = True
        else:
            edit_mode = False

        current_info = getCurrentInfo(self.template, page_id, edit_mode)

        return self.servePage(page_id, current_info)


    @amiweb.expose
    @editPermission
    def pageCreate(self, parent_id, name):
        new_page = Model.Pages.insertPageFromWiki(parent_id, name)
        AmiCache.expireCache(parent_id)
        raise amiweb.HTTPFound("%s/siteedit/?page_id=%i" % (BASE_URL, new_page.id))

    @amiweb.expose
    @editPermission
    def pageLinkUpdate(self, page_link_id, new_page_id, current_pid):
        Model.PageLinks.set(page_link_id, new_page_id)
        AmiCache.expireCache(current_pid)
        page = Model.Pages.getPageById(new_page_id)
        return "%s/siteedit/%s" % (BASE_URL, page.getFullLink())

    @amiweb.expose
    def setViewEditMode(self, value, page_id):
        Users.setViewEditMode(value)
        return Pages.getContent(page_id, no_json=0)


class MenuEdit:

    def __init__(self, menu_tmpl_obj):
        self.template = PluginTemplate("MenuEdit")
        self.menu_tmpl_obj = menu_tmpl_obj

    def filter(self):
        Site.checkAdminPermission()
        Users.checkCurrentPagePermissions()

    @amiweb.expose
    def index(self):
        menu_obj = Model.Menus.getStandardMenu()
        ns = {'template': self.template,
              'menu_obj': menu_obj}
        return render("skeletonz/view/whole_sites/menu_edit.tmpl", ns)

    @amiweb.expose
    def getHTML(self):
        current_info = {
          'logged_in': True
        }
        return self.menu_tmpl_obj.renderText(current_info)

    @amiweb.expose
    def add(self, name, order, menu_id):
        item = Model.MenuItems.add(name, order, menu_id)
        try:
            return item.toJSON()
        except:
            raise amiweb.AppError("Error encoutered")

    @amiweb.expose
    def addExternal(self, name, url, order, menu_id):
        item = Model.MenuItems.addExternal(name, url, order, menu_id)
        return item.toJSON()

    @amiweb.expose
    def delete(self, id):
        id = json.read(id)
        Model.MenuItems.delete(int(id))
        return "ok"

    @amiweb.expose
    def update(self, id, name):
        Model.MenuItems.update(id, name)
        return Model.MenuItems.getItemById(id).toJSON()

    @amiweb.expose
    def updateExternal(self, id, name, url):
        Model.MenuItems.updateExternal(id, name, url)
        return Model.MenuItems.getItemById(id).toJSON()

    @amiweb.expose
    def setNewOrder(self, item1, item2):
        item1 = json.read(item1)
        item2 = json.read(item2)

        Model.MenuItems.setNewOrder(item1['id'], item1['order'])
        Model.MenuItems.setNewOrder(item2['id'], item2['order'])

        res = json.write({
            'item1_json': Model.MenuItems.getItemById(item1['id']).toJSON(),
            'item2_json': Model.MenuItems.getItemById(item2['id']).toJSON()})
        return res


class Pages:

    def __init__(self):
        self.template_insert_plugin = PluginTemplate("InsertPlugin")
        self.template_show_prop = PluginTemplate("PageProperties")
        self.template_pagelog = PluginTemplate("PageLog")

        #Configure the templates
        self.template_insert_plugin.getHeader().appendStaticCoreScript("/amibar/amiformat.js")

        self.template_show_prop.getHeader().appendStaticCoreScript("/scripts/Content.js")

    def filter(self):
        Users.checkLogin()
        Users.checkCurrentPagePermissions()

    @amiweb.expose
    def setTitle(self, data):
        data = json.read(data)

        #Update Page with new title
        Model.Pages.setPageTitle(data['page_id'], data['new_val'])

        AmiCache.expireCurrentPage()
        return "ok"

    @amiweb.expose
    def setParent(self, page_id, parent_id):
        page_id = int(page_id)
        parent_id = int(parent_id)

        if parent_id == 0:
            parent_id = None

        current_page = Model.Pages.getPageById(page_id)

        if parent_id:
            parent_page = Model.Pages.getPageById(parent_id)

            #Make sure the current page isn't a parent for that parent
            parents = parent_page.getParentList()
            for pa in parents:
                if pa.id == page_id:
                    raise amiweb.AppError("You can't changes a parents child to be a parent of the parent... I.e. recursion problem!")

        old_parent = current_page.getParent()
        Model.Pages.setParent(page_id, parent_id)

        #Expire havoc
        AmiCache.expireCache(page_id)

        if old_parent:
            AmiCache.expireCache(old_parent.id)
        if parent_id:
            AmiCache.expireCache(parent_id)
        return "ok"

    @amiweb.expose
    def setContent(self, page_id, new_val=""):
        new_val = unicode(new_val, 'utf8')
        new_val = new_val.replace("\r\n", "\n")

        #Update Page with new title
        Model.Pages.setPageContent(page_id, new_val)

        AmiCache.expireCache(page_id)
        if Users.getViewEditMode() == 'on':
            result = Model.Pages.getPageById(page_id).getContentEdit()
        else:
            result = Model.Pages.getPageById(page_id).getContent()
        return result

    @amiweb.expose
    def getContent(self, page_id, no_json=1):
        page_obj = Model.Pages.getPageById(page_id)
        if no_json == 1:
            if page_obj.content == None:
                return ""
            else:
                content = page_obj.content
                content = content.replace("\n", "\r")
                return content
        else:
            if Users.getViewEditMode() == 'on':
                return page_obj.getContentEdit()
            else:
                return page_obj.getContent()

    @amiweb.expose
    def deleteFile(self, id):
        Admin.UploadManager().delete(id)
        return "ok"

    @amiweb.expose
    def changePremission(self, page_id, type, value):
        username = amiweb.session()['username']
        if username == ADMIN_USERNAME or UserModel.Users.isAdmin(username):
            Model.Pages.changePagePremissions(page_id, type, value)
        return "ok"

    @amiweb.expose
    def deletePage(self, id):
        #TODO: Refactor this (and other methods) to plugins/wikipages
        page = Model.Pages.getPageById(id)
        url_mapper.expirePagePath(page.getFullLink())

        current_page = Model.Pages.getPageById(getCurrentPage())
        page_name = Model.Pages.getPageById(id).name
        Model.Pages.deletePageById(id)

        AmiCache.expireAllPages()

        #Find the right plugin args
        args = getFormatManager().getPluginArguments('page', page_name)
        html = wiki_filter.wikiWords(args, True, current_page)
        return html

    @amiweb.expose
    def viewInsertPlugin(self):
        ns = {
          'template': self.template_insert_plugin,
          'plugins': getPluginManager().listAvailablePlugins()
        }
        return render("skeletonz/view/whole_sites/insert_plugin.tmpl", ns)

    @amiweb.expose
    def setPageHidden(self, page_id, value):
        Users.checkCurrentPagePermissions()
        Model.Pages.setPageHidden(page_id, value)

        #Expire the parent also
        page_obj = Model.Pages.getPageById(page_id)
        if page_obj.parent_id:
            AmiCache.expireCache(page_obj.parent_id)

        #Expire every page in the menu
        for m_item in Model.MenuItems.getItemsByMenuId(1):
            AmiCache.expireCache(m_item.page_id)

        AmiCache.expireCurrentPage()
        return value

    @amiweb.expose
    def showPageProperties(self, page_id):
        page_obj = Model.Pages.getPageById(page_id)
        parent = page_obj.getParent()

        #Get the sitemap
        parent_site_map = visit.createOptions(parent, visit.getSitemap(), page_obj.id, "updateParent", null_item=False)

        files = upload_model.Files.getAllFilesOnPage(page_obj)
        ns = {'template': self.template_show_prop,
              'page_id': page_id,
              'page_obj': page_obj,
              'files': files,
              'is_admin': Users.isAdmin(),
              'parent_site_map': parent_site_map,
              'getPermissionDropBox': Admin.PermissionManager.getPermissionDropBox}
        functions = 'skeletonz/view/admin/components/page_files.tmpl'
        return render("skeletonz/view/whole_sites/page_properties.tmpl", ns, base=functions)

    ##
    # PageLogs
    #
    @amiweb.expose
    def getAllLogs(self, page_id):
        l = []
        for item in Model.PageLogs.getAllLogs(page_id):
            t = item['time_of_edit']
            item['time_of_edit'] = t.strftime("%d. %b %Y - %H:%M")
            l.append(item.toJSON())
        return json.write(l)

    @amiweb.expose
    def diffRevisions(self, page_id, rev1, rev2):
        obj_rev1 = Model.PageLogs.getLogByRev(page_id, rev1)
        obj_rev2 = Model.PageLogs.getLogByRev(page_id, rev2)

        if obj_rev1 and obj_rev2:
            result = diff.textDiff(obj_rev1.text, obj_rev2.text)
            result = result.replace("<", "&lt;").replace(">", "&gt;")
            result = re.sub("&lt;(/?(ins|del).*?)&gt;", r"<\1>", result)
            return result #result.replace('\n', '<br />')
        else:
            errors = []
            if not obj_rev1: errors.append("Revision %s not found" % rev1)
            if not obj_rev2: errors.append("Revision %s not found" % rev2)
            raise amiweb.AppError("\n".join(errors))

    @amiweb.expose
    def revert(self, page_id, rev):
        obj_rev = Model.PageLogs.getLogByRev(page_id, rev)
        Model.Pages.setPageContent(page_id, obj_rev.text)
        AmiCache.expireCache(page_id)
        return 'ok'


    @amiweb.expose
    def showPageLog(self, page_id):
        page_obj = Model.Pages.getPageById(page_id)

        ns = {'template': self.template_pagelog,
              'page_obj': page_obj,
              'all_logs': self.getAllLogs(page_id)}
        return render("skeletonz/view/whole_sites/page_log.tmpl", ns)

##
# Singletons
#
Pages = Pages()


##
# Can be used from plugins to fake a page object
#
class PageDeco:

    def __init__(self, kw):
        self.title = kw['title']
        self.parent_id = kw['host_page']
        self.id = kw['id']
        self.no_cache = kw.get('no_cache', False)
        self.hidden = kw['hidden']
        self.content = kw['content']
        self.edit_mode = kw['edit_mode']
        self.premission_type = kw['premission_type']

    def getContent(self):
        return self.content

    def generateContent(self):
        return ''

    def generateContentEdit(self):
        return ''

    def getMenu(self):
        return Model.Menus.getStandardMenu()

    def getRootParent(self):
        parent_id = self.parent_id
        class CurrentItem:
            def __init__(self):
                self.id = parent_id
            def getClass(self):
                return ''
        return CurrentItem()

    def servePage(self, template=None):
     	print "[Site] PageDeco servePage:here<br>"
        if self.edit_mode:
            site_obj = getRootController().root_obj.siteedit
        else:
            site_obj = getRootController().root_obj

        if not template:
            template = site_obj.template

        self._hideNavigation(site_obj.template)

        current_info = getCurrentInfo(template, self, self.edit_mode)
        content = site_obj.servePage(self.id, current_info)
        return content

    def _hideNavigation(self, template):
        style = """
        #navigation { display: none; }
        #CMS_HeadLine { display: none; }
        """
        template.getHeader().appendStyleData(style, True)
