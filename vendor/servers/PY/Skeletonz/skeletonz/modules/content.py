from amilib.template import render
from amilib.amiweb import amiweb
from skeletonz.model import CMSModel
from skeletonz.mylib.converters import appendSiteEdit

from skeletonz import server

class Content:

    def __init__(self, edit_mode=False):
        self.edit_mode = edit_mode
        self.edit_permission = True
        self.logged_in = False
        self.bodyclass = ''
        self.template = None #Is set from skeletonz/server.py - markChildren
        self.file_name = 'site_content.tmpl'

    def setEditMode(self, edit_mode):
        self.edit_mode = edit_mode

    def setEditPermission(self, p):
        self.edit_permission = p

    def setLoggedIn(self, p):
        self.logged_in = p

    def setPage(self, page_obj):
        self.page_obj = page_obj

    def setBodyClass(self, bclass):
        self.bodyclass = bclass

    def getParentList(self):
        parent = CMSModel.Pages.getPageById(self.page_obj.parent_id)
        parent_list = []
        while parent != None:
            parent_list.insert(0, parent)
            parent = CMSModel.Pages.getPageById(parent.parent_id)
        return parent_list


    ##
    # Template macros
    def renderText(self):
        from skeletonz import Site
        if Site.Users.getViewEditMode() == 'off':
            self.edit_mode = False

        #Set the URL
        URL = server.getConfig().BASE_URL
        self.URL = URL

        ns = {'template': self.template,
              'BASE_URL': URL,
              'parent_list': self.getParentList(),
              'page_obj': self.page_obj,
              'bodyclass': self.bodyclass,
              'edit_mode': self.edit_mode,
              'edit_permission': self.edit_permission,
              'logged_in': self.logged_in,
              'appendSiteEdit': appendSiteEdit(self.logged_in)
              }
        return render("templates/%s/view/%s" % (self.template.getName(), self.file_name), ns)
