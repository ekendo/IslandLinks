from skeletonz.server import getConfig
from skeletonz.modules import template
from amilib.template import render

import skeletonz.modules as modules

#Template name is pretty important since it controls the directory structure
NAME = "Default_Template"

class SiteTemplate(template.Template):

    def __init__(self):
        self.name = NAME

        #Set header
        self.header = modules.header.SiteHeader()
        self.header.appendStyle("%s/%s/static/styles/gui.css" % (getConfig().BASE_URL, self.name) )
        self.header.appendStyle("%s/%s/static/styles/content.css" % (getConfig().BASE_URL, self.name) )

        #Set content
        self.content = modules.content.Content()

        #Set menu
        self.menu = modules.menu.ListMenu()
        modules.sections.mapSection("cms_list_menu", self.menu.renderText)

        #Set footer
        self.footer = modules.footer.Footer()

    #def plugin_blog_renderPost(self, ns):
    #  return render('templates/Default_Template/view/blog_post.tmpl', ns)

    def getHeader(self):
        return self.header