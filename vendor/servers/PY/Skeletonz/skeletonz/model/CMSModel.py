import re
import types
import os
import urllib
import datetime

from amilib import json
from amilib.amiweb.amiweb import db, session

from skeletonz.server import getConfig, getPlugins, getFormatManager, getCurrentPage, getMainPageId

from skeletonz.mylib.converters import makeLinkAble
from skeletonz.mylib.splitter import splitter


def getAllTables():
    """
    Returns all tables that is used by Skeletonz and its plugins
    """
    tables = []
    for t in db().query('SHOW TABLES;'):
        t = t.values()[0]
        tables.append(t)

    def fn(x):
        if x.find(getConfig().TABLE_PREFIX) == 0:
            return True
        else:
            return False
    return filter(fn, tables)

##
# Menu
#
class MenuItemDeco:
    """
    Following attributes are added to an object of this class:
      id, url, name, m_order, menu_id, page_id,  type
    """

    def getClass(self):
        return makeLinkAble(self.name)

    def getPage(self):
        return Pages.getPageById(self.page_id)

    def toJSON(self):
        if self.type == 'external':
            d = {'id': self.id,
                'url': self.url,
                'name': self.name,
                'content': self.name,
                'order': self.m_order,
                'type': self.type,
                'class': makeLinkAble(self.name)
            }
        else:
            d = {'id': self.id,
                'm_page_id': self.page_id,
                'name': self.name,
                'content': self.name,
                'order': self.m_order,
                'type': self.type,
                'class': makeLinkAble(self.name)
            }

        if self.page_id == getMainPageId():
            d['type'] = 'mainPage'
        return json.write(d)


class MenuItems:

    def getItemById(self, id):
        return db().select("menu_item", id=id, as_one=True, obj_deco=MenuItemDeco)

    def getItemByPageId(self, page_id):
        return db().select("menu_item", page_id=page_id, as_one=True, obj_deco=MenuItemDeco)

    def getItemsByMenuId(self, menu_id):
        return db().select("menu_item", menu_id=menu_id, obj_deco=MenuItemDeco, order_by="m_order")

    def getItemByName(self, name):
        return db().select("menu_item", name=name, obj_deco=MenuItemDeco)

    def add(self, title, order, menu_id):
        page = Pages.insertPage(title, None, menu_id)
        if page == None:
            return None
        menu_item_id = db().insert("menu_item", name=title, m_order=order, menu_id=menu_id, page_id=page.id, type="page")
        return self.getItemById(menu_item_id)

    def addExternal(self, alias, url, order, menu_id):
        menu_item_id = db().insert('menu_item', name=alias, url=url, m_order=order, menu_id=menu_id, page_id=None, type="external")
        return self.getItemById(menu_item_id)

    def delete(self, id):
        menu_item = self.getItemById(id)
        if menu_item == None:
            return

        if menu_item.type == "external":
            self.deleteOnly(id)
        else:
            #The actual menu-item is delelted in deletePageById
            try:
                Pages.deletePageById(menu_item.page_id)
            except:
                pass

    def deleteOnly(self, id):
        db().delete("menu_item", id=id)

    def update(self, id, name):
        db().update("menu_item", id=id, name=name)

    def updateExternal(self, id, name, url):
        db().update("menu_item", id=id, name=name, url=url)

    def setNewOrder(self, id, order):
        db().update("menu_item", id=id, m_order=order)


class MenuDeco:
    """
    Following attributes are added to an object of this class:
      id, name, primary_menu
    """
    def getTopItem(self):
        return db().select("menu_item", menu_id=self.id, order_by="m_order", obj_deco=MenuItemDeco, limit=1, as_one=True)

    def getAllPageItems(self):
        return db().select("menu_item", "page_id is not null", menu_id=self.id, order_by="m_order", obj_deco=MenuItemDeco)

    def getAllItems(self):
        return MenuItems.getItemsByMenuId(self.id)

    def getPagesInMenu(self):
        menu_pages = []
        for mi in self.getAllPageItems():
            try:
                menu_pages.append(mi.getPage())
            except:
                pass
        return menu_pages

    def getPageItem(self, page_id):
        page_obj = MenuItems.getItemByPageId(page_id)
        if page_obj != None:
            return page_obj
        else:
            met_none = False
            page_obj = Pages.getPageById(page_id)

            top_parent = Pages.getPageById(page_obj.parent_id)
            while met_none != True:
                if top_parent.parent_id in [None, 0]:
                    met_none = True
                else:
                    top_parent = Pages.getPageById(top_parent.parent_id)

            return MenuItems.getItemByPageId(top_parent.id)

    def isPageInMenu(self, page_name):
        page = db().select("menu_item", name=page_name, menu_id=self.id, as_one=True)
        return page != None

    def accept(self, visitor):
        return visitor.visitMenu(self, self.getPagesInMenu())

    def toJSON(self):
        d = {'id': self.id,
            'name': self.name,
            'content': self.name}
        return json.write(d)


class Menus:

    def getMenuById(self, id):
        return db().select("menu", id=id, as_one=True, obj_deco=MenuDeco)

    def getMenuByName(self, name):
        return db().select("menu", name=name, as_one=True, obj_deco=MenuDeco)

    def getStandardMenu(self):
        print "[CMSModel]getStandardMenu:<br>"
        return self.getMenuByName('Standard')

    def getAllMenus(self):
        return db().select("menu", order_by="id", obj_deco=MenuDeco)

    def checkExsist(self, name):
        return len(db().select("menu", name=name)) != 0

    def add(self, name, primary=False):
        new = db().insert("menu", name=name, primary_menu=primary)
        return self.getMenuById(new)

    def delete(self, id):
        menu = self.getMenuById(id)
        if menu.name == "Standard":
            return

        #Delete all the items in the menu
        db().delete("menu_item", menu_id=id)
        db().delete("menu", id=id)

    def update(self, id, name):
        db().update("menu", id=id, name=name)


class PageFilters:
    def __init__(self):
        self.filters = []
        self.combinators = []

    def initPluginFilters(self):
        for plugin in getPlugins():
            plugin = plugin['module']
            try:
                f = plugin.returnContentFilters()
                self.filters.extend(f)
            except NotImplementedError:
                pass

            try:
                f = plugin.returnContentCombinators()
                self.combinators.extend(f)
            except NotImplementedError:
                pass

    def amiformat(self, text, edit_mode, wrap_in_p, page):
        try:
            result_text = amiformat.amiformat(text, not wrap_in_p, page.getFullLink())
        except:
            result_text = text
        return result_text

    def filterContent(self, content, edit_mode, wrap_in_p, page=None):
        if not page:
            try:
                page = Pages.getPageById(session()['current_page_id'])
            except KeyError:
                page = Pages.getPageById(getMainPageId())

        result = self.amiformat(content, edit_mode, wrap_in_p, page)
        result = splitter(result, page_filters.filters, page_filters.combinators, edit_mode, page)
        result = result.replace("[!", "[")
        return result


class PageDeco:
    """
    Following attributes are added to an object of this class:
      id, name, title, content, premission_type, premission_value, hidden, menu_id, parent_id
    """
    def __init__(self):
        self.generated_cnt = None
        self.generated_cnt_edit = None

    def toJSON(self):
        menu = Menus.getMenuById(self.menu_id)
        try:
            menu_name = menu.name
        except:
            menu_name = "None"
        d = {'id': self.id,
            'title': "%s [%s]" % (self.title, menu_name),
            'name': self.name,
            'content': self.name,
        }
        return json.write(d)

    def getRootParent(self):
        if self.parent_id == None:
            return self
        else:
            return Pages.getPageById(self.parent_id).getRootParent()

    def getParent(self):
        return Pages.getPageById(self.parent_id)

    def getParentList(self):
        parent_list = []
        parent_id = self.parent_id

        while parent_id != None:
            p = Pages.getPageById(parent_id)
            parent_list.insert(0, p)
            parent_id = p.parent_id
        return parent_list

    def getChildren(self):
        pa = db().select("page", parent_id=self.id, obj_deco=PageDeco)
        if len(pa) == 0:
            return None
        else:
            return pa

    def getMenu(self):
        menu = Menus.getMenuById(self.menu_id)
        if menu != None:
            return menu
        else:
            #The menu is gone, replace it with the standard menu
            menu = Menus.getStandardMenu()
            db().update("page", id=self.id, menu_id=menu.id)
            return menu

    def getTitle(self):
        return self.title

    def getClass(self):
        return makeLinkAble(self.name)

    def accept(self, visitor):
        return visitor.visitPage(self, self.getChildren())

    def generateContent(self):
        if self.content == "" or self.content == None:
            self.generated_cnt = '<p>This page has no content.</p>'
        else:
            self.generated_cnt = getFormatManager().htmlFormat(self.content, False, True, self)

    def generateContentEdit(self):
        if self.content == "" or self.content == None:
            self.generated_cnt_edit = '<p>This page has no content.</p>'
        else:
            self.generated_cnt_edit = getFormatManager().htmlFormat(self.content, True, True, self)

    def getContent(self):
        if not self.generated_cnt:
            self.generateContent()
        return self.generated_cnt

    def getContentEdit(self):
        if not self.generated_cnt_edit:
            self.generateContentEdit()
        return self.generated_cnt_edit

    def getFullLink(self):
        parent_list = self.getParentList()
        parent_list = map(lambda x: x._getLinkName(), parent_list)
        parent_list.append(self._getLinkName())
        return "%s/" % "/".join(parent_list)

    def _getLinkName(self):
        name = self.name
        if not name:
            name = self.title

        if type(name) == types.UnicodeType:
            name = name.encode("utf8")
        name = name.replace(' ', '_')
        return urllib.pathname2url(name)



class Pages:

    def setPageHidden(self, page_id, value):
        db().update("page", id=page_id, hidden=value)

    def setPageTitle(self, id, new_val):
        db().update("page", id=id, title=new_val)

    def setPageName(self, id, new_val):
        db().update("page", id=id, name=new_val)

    def setPageContent(self, id, new_val):
        PageLogs.registerUpdate(id, new_val)
        db().update("page", id=id, content=new_val)

    def setPageMenu(self, id, menu_id):
        db().update("page", id=id, menu_id=menu_id)

    def setParent(self, id, parent_id):
        if id != parent_id:
            db().update("page", id=id, parent_id=parent_id)

    def getPage(self, name, parent_id):
        page = db().select("page", name=name, parent_id=parent_id, as_one=True, obj_deco=PageDeco)
        if page == None:
            page = db().select("page", title=name, parent_id=parent_id, as_one=True, obj_deco=PageDeco)
        return page

    def getPageGlobal(self, name):
        """Global pages don't have parents."""
        return db().select("page", name=name, as_one=True, obj_deco=PageDeco)

    def getPageById(self, id):
        if id == None or id == "":
            return None

        try:
            id = int(id)
        except:
            return None

        return db().select("page", id=id, as_one=True, obj_deco=PageDeco)

    def getFrontPage(self):
        return self.getPageById(getMenuById())

    def getAllPages(self):
        return db().select("page", order_by="id", obj_deco=PageDeco)

    def getAllPagesWhereTextIsUsed(self, text):
        pages = db().select("page", "content RLIKE '" + text + "'", obj_deco=PageDeco)
        return pages

    def insertPage(self, title, content, menu_id):
        #Check if the page is already found in that menu
        menu = Menus.getMenuById(menu_id)
        if menu.isPageInMenu(title):
            return None
        p = db().insert("page", name=title, title=title, content=content, menu_id=menu_id, hidden=1)
        return self.getPageById(p)

    def insertPageFromWiki(self, parent_id, title):
        parent = self.getPageById(parent_id)
        p = db().insert("page", name=title, title=title, content="", menu_id=parent.menu_id, parent_id=parent_id, hidden=1, premission_type=parent.premission_type, premission_value=parent.premission_value)
        return self.getPageById(p)

    def deletePageById(self, id):
        #If the page has a menu item, then delete it too
        db().delete("menu_item", page_id=id)

        #Delete the children too
        page = self.getPageById(id)
        if page.getChildren() != None:
            for child in page.getChildren():
                self.deletePageById(child.id)

        db().delete("page", id=id)

    def getPremission(self, id):
        p = db().select("page", id=id, cols=["premission_type", "premission_value"], as_one=True)
        return (p.premission_type, p.premission_value)

    def changePagePremissions(slef, id, type, value):
        db().update("page", id=id, premission_type=type, premission_value=value)

    def resetPremissions(self, id):
        db().update("page", id=id, premission_type="Every user", premission_value=None)

    def resetUserPagePremissions(self, username):
        pages = db().select("page", premission_type="user", premission_value=username)
        for p in pages:
            self.resetPremissions(p.id)

    def resetGroupPagePremissions(self, name):
        pages = db().select("page", premission_type="group", premission_value=name)
        for p in pages:
            self.resetPremissions(p.id)


class PageLogs:

    def registerUpdate(self, page_id, text):
        #Register only if there is something new
        l_log = self.getLatestLog(page_id)

        if l_log and l_log.text.__hash__() == text.__hash__():
            return

        user = session()['username']

        #Select the current revision
        revision = db().select('pagelog', page_id=page_id, order_by='revision')
        if len(revision) == 0:
            revision = 1
        else:
            revision = revision[-1].revision + 1

        time = "%s" % datetime.datetime.now()
        db().insert('pagelog', edited_by=user, page_id=page_id, text=text, revision=revision, time_of_edit=time)

    def getAllLogs(self, page_id):
        cols = ['id', 'edited_by', 'revision', 'time_of_edit']
        return db().select('pagelog', cols=cols, page_id=page_id, order_by="revision", reversed=True)

    def getLogByRev(self, page_id, rev):
        return db().select('pagelog', page_id=page_id, revision=rev, as_one=True)

    def getLatestLog(self, page_id):
        return db().select('pagelog', page_id=page_id, limit=1, order_by='revision', reversed=True, as_one=True)


class PageLinkDeco:

    def getPage(self):
        return Pages.getPageById(self.page_id)


class PageLinks:

    def getPageLinkByIdent(self, ident):
        return db().select("page_link", ident=ident, as_one=True, obj_deco=PageLinkDeco)

    def getPageLinkById(self, id):
        return db().select("page_link", id=id, as_one=True, obj_deco=PageLinkDeco)

    def deletePageLinkById(self, id):
        db().delete("page_link", id=id)

    def set(self, ident_id, page_id):
        if page_id == 0:
            db().update("page_link", id=ident_id, page_id=None)
        else:
            db().update("page_link", id=ident_id, page_id=page_id)

    def createPageLink(self, ident):
        id = db().insert("page_link", ident=ident)
        return self.getPageLinkById(id)



#Static objects
MenuItems = MenuItems()
Menus = Menus()
Pages = Pages()
PageLinks = PageLinks()
PageLogs = PageLogs()
