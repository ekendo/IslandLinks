from skeletonz.server import getConfig
from skeletonz.modules import template

import skeletonz.modules as modules

#Template name is pretty important since it controls the directory structure
NAME = "Dummy_Template"

class SiteTemplate(template.Template):

    def __init__(self):
        self.name = NAME

        #Set header
        self.header = modules.header.SiteHeader()
        self.header.appendStyle("%s/%s/static/styles/gui.css" % (getConfig().BASE_URL, self.name) )

        #Set menu
        self.menu = modules.menu.ListMenu()
        modules.sections.mapSection("cms_list_menu", self.menu.renderText)

        #Set content
        self.content = modules.content.Content()

        #Set footer
        self.footer = modules.footer.Footer()
    def getHeader(self) :
        return header