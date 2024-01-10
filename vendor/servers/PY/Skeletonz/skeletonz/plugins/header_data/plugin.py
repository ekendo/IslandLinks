import re

from skeletonz.server import getFormatManager
from skeletonz.modules.plugin import GenericPlugin

PLUGINS_FOR_EXPORT = ['HeaderData']

class HeaderData(GenericPlugin):
    NAME = "Header data plugin"
    DESCRIPTION = "Add extra JavaScript or CSS with this plugin."
    SYNTAX = [
        {'handler': 'header_data',
          'required_arguments': {'data': {'type': 'text', 'help': 'The actual header code. <br /> Could be JavaScript or CSS wraped in &lt;script&gt; and &lt;style&gt; tags.'}},
         'optional_arguments': {}
        }
      ]

    def __init__(self):
        self.data = None

        getFormatManager().registerMLPlugin('header_data', self.handleFilter)

    def add(self, template, on_init):
        if self.data and not on_init:
            template.getHeader().appendScriptData(self.data)
        self.data = None

    def addToSiteEditTemplate(self, template, on_init):
        self.add(template, on_init)

    def addToSiteTemplate(self, template, on_init):
        self.add(template, on_init)

    def handleFilter(self, args, edit_mode, page):
        data = args.get('data', None)
        if not data:
            return True, ''
        self.data = data.strip()
        return True, ''
