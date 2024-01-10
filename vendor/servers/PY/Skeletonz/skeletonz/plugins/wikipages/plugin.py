import os

from skeletonz.modules.plugin import GenericPlugin
from skeletonz.server import getConfig, getFormatManager

import filter

PLUGINS_FOR_EXPORT = ['WikiPlugin']

class WikiPlugin(GenericPlugin):
    NAME = "Wiki plugin"
    DESCRIPTION = "Adds syntax so you can create under pages."
    SYNTAX = [
        {'handler': 'page',
         'required_arguments': {'ident': 'The identification'},
         'optional_arguments': {'global': {'type': 'option', 'help': 'A global page is a page without a parent.'}}
        },
        {'handler': 'pagelink',
         'required_arguments': {'ident': 'The page ident, i.e. the page name<br />that you would like to link to'},
         'optional_arguments': {}
        }
      ]

    def __init__(self):
        getFormatManager().registerSLPlugin('page', self.handlePages)
        getFormatManager().registerSLPlugin('pagelink', self.handlePageLinks)

    def addToSiteEditTemplate(self, template, on_init):
        if on_init:
            header = template.getHeader()
            path = '%s/skeletonz/plugins/wikipages/static' % os.getcwd()
            header.appendScript("%s/Page.js" % path)
            header.appendScript("%s/Pagelink.js" % path)

    def addToController(self, rc):
        path = "skeletonz/plugins/wikipages/static"
        rc.addStaticPath("/static_plugin/wikiplugin/", path)

    def handlePages(self, args, edit_mode, page):
        return False, filter.wikiWords(args, edit_mode, page)

    def handlePageLinks(self, args, edit_mode, page):
        return False, filter.wikiPageLink(args, edit_mode, page)
