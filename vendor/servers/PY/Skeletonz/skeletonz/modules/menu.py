from amilib.template import render
from skeletonz import server
from skeletonz.model import CMSModel
from skeletonz.mylib.converters import appendSiteEdit
import os

class Menu:

    def __init__(self):
        self.logged_in = False
        self.template = None #Is set from skeletonz/server.py - markChildren

    def setMenu(self, m):
        self.menu_obj = m

    def renderText(self, current_info, file):
        """
        menu: An instance of model.menu.Menu class
        """
        logged_in = current_info['logged_in']
        ns = {'template': self.template,
              'menu_obj': CMSModel.Menus.getStandardMenu(),
              'logged_in': logged_in,
              'appendSiteEdit': appendSiteEdit(logged_in)}
        return render(file, ns)


class ListMenu:
    def __init__(self):
        self.menu = Menu()

    def setMenu(self, m):
        self.menu.setMenu(m)

    def renderText(self, current_info):
        return self.menu.renderText(current_info, 'skeletonz/view/general_templates/list_menu.tmpl')
