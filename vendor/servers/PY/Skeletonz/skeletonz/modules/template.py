import header
import footer
import menu
import content

from skeletonz.server import getConfig, getPlugins

def initTemplate(template, func):
    #Let plugins alter template
    #print "[template]initTemplate:<br>"
    for plugin in getPlugins():
        plugin = plugin['module']
        try:
            eval("plugin.%s(template, True)" % func)
        except NotImplementedError:
            pass

class Template:
    """ Used as an interface.

    Provides accessor and mutator functions.
    """
    def __init__(self):
        self.header = None
        self.content = None
        self.footer = None
        self.menu = None

    def setHeader(self, new_header):
        self.header = new_header

    def getHeader(self):
        if self.header is None: raise Exception("The header is None")
        else: return self.header

    def setContent(self, new_content):
        self.content = new_content

    def getContent(self):
        if self.content is None: raise Exception("The content is None")
        else: return self.content

    def setMenu(self, m):
        self.menu = menu

    def getMenu(self):
        return self.menu

    def setFooter(self, new_footer):
        self.footer = new_footer

    def getFooter(self):
        if self.footer is None: raise Exception("The footer is None")
        else: return self.footer

    def getName(self):
        return self.name

    def markChildren(self):
        self.getHeader().template = self

        try: self.getContent().template = self
        except: pass
        try: self.getMenu().template = self
        except: pass

        self.getFooter().template = self


class AdminTemplate(Template):

    def __init__(self, title="Admin section"):
        #Set header
        self.header = header.AdminHeader(title)

        self._appendGlobalStyle()
        self._appendMenuController()

        #Set footer
        self.footer = footer.Footer( "footer" )

        self.markChildren()

    def _appendGlobalStyle(self):
        self.header.appendScript('%s/static_core/scripts/general/indicator.js' % getConfig().BASE_URL)

        self.header.appendStaticCoreStyle("/styles/action_links.css")
        self.header.appendStaticCoreStyle("/admin/styles/gui.css")
        self.header.appendStaticCoreStyle("/admin/styles/content.css")

    def _appendMenuController(self):
        self.header.appendStaticCoreScript("/admin/scripts/menu_controller.js")
        self.header.appendStaticCoreStyle("/admin/styles/menu_controller.css")
        self.header.appendStaticCoreScript("/admin/scripts/list.js")
        self.header.appendListCommanderScripts()



class PluginTemplate(AdminTemplate, object):

    def __init__(self, template_name="core"):
        super(PluginTemplate, self).__init__("PluginTemplate_%s" % template_name)


class ShowTemplate(Template):

    def __init__(self, title):
        #print "[template] ShowTemplate _init_:<br>"
        self.header = header.ShowHeader(title)
        self.footer = footer.Footer( "footer" )
        self.header.appendStaticCoreStyle("/styles/show_template.css")
        self.markChildren()


class SiteEditTemplate(Template):

    def __init__(self, SiteTemplate):
        #Get the noram styles and scripts, plus the ones used for editing
        obj_site_template = SiteTemplate()

        self.header = obj_site_template.getHeader()

        #Set prefix
        self.header.setPrefix("SiteEditTemplate")

        #Init
        self.header.appendStaticCoreScript('/scripts/general/init_edit.js')

        self.header.appendStaticCoreStyle("/styles/edit.css")
        self.header.appendStaticCoreStyle("/styles/action_links.css")
        self.header.appendStaticCoreScript('/scripts/general/indicator.js')

        self.header.appendAJS()
        self.header.appendGoogieSpell()
        self.header.appendListCommanderScripts()

        #Greybox
        self.header.appendStaticCoreScript("/greybox/gb_scripts.js")

        #Editable components
        self.header.appendEditorScripts()
        self._appendEditableCompoenents()

        #Set the edit mode so pages can be edited
        obj_site_template.getContent().setEditMode(True)

        self.content = obj_site_template.getContent()
        self.menu = obj_site_template.getMenu()

        self.footer = obj_site_template.getFooter()

        #Set name
        self.name = obj_site_template.getName()

        self.markChildren()

    def _appendEditableCompoenents(self):
        ec_path = '%s/scripts' % self.header.sc_path
        self.header.appendScript('%s/Headline.js' % ec_path)
        self.header.appendScript('%s/Menu.js' % ec_path)
        self.header.appendScript('%s/Status.js' % ec_path)
