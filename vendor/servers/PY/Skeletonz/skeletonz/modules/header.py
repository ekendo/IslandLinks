import os, sys
import time
from datetime import date
from skeletonz.server import getConfig
from amilib.template import render
from skeletonz.mylib import converters, truncator

generated_dir = "dynamic_dirs/generated"

class Truncator:
    """
    This module sets scripts and styles togheter accordning to their
    header
    """

    def __init__(self, prefix):
        self.prefix = prefix
	#print "[header] Truncator: getConfig().BASE_URL"
        self.rel_template = '%s/static/' % getConfig().CURRENT_TEMPLATE.NAME
        self.path_template = "%s/templates/%s/static/" % (os.getcwd(), getConfig().CURRENT_TEMPLATE.NAME)
        self.path_static_core = "%s/skeletonz/static/" % os.getcwd()
        self.path_static_template = "%s/%s/static/" % (os.getcwd(), getConfig().CURRENT_TEMPLATE.NAME)

        self.memo_css = {}
        self.memo_js = {}

    def setPrefix(self, p):
        self.prefix = p
        self.memo_css = {}
        self.memo_js = {}

    def getRelative(self, url):
        url = url.replace(getConfig().BASE_URL, "")
        if url.find("/static_core/") == 0:
            url = "%s%s" % (self.path_static_core, url.replace("/static_core/", ""))
        if url.find(self.rel_template) == 0:
            url = "%s%s" % (self.path_template, url.replace(self.rel_template, ""))
        #No it should be realive
        return url

    def getRelativeList(self, list_of_urls):
        """
        Returns the relative list of URL's. I.e. their absolute path on the sys
        """
        new_list = []
        for url in list_of_urls:
            url = self.getRelative(url)
            new_list.append(url)
        return new_list

    def buildList(self, l, static_l, type):
        """
        Looks through the list to see if the static files have been updated/created
        If they have, we build a new cache
        If not, we return the cached version
        """
        is_updated = True
        new_memo = {}
        
        for url in l:
            new_memo[url] = date.today()
            """
            last_modif = os.stat(self.getRelative(url)).st_mtime
            if type == "css":
                v = self.memo_css.pop(url, None)
            elif type == "js":
                v = self.memo_js.pop(url, None)
            #print "%s == %s" % (last_modif, v)
            if v == None or v < last_modif:
                is_updated = False

            #Update new_memo
            new_memo[url] = last_modif
	    """
	
        if type == "css":
            file = "%s/%s/%s_styles.css" % (os.getcwd(), generated_dir, self.prefix)
            self.memo_css = new_memo
        elif type == "js":
            file = "%s/%s/%s_scripts.js" % (os.getcwd(), generated_dir, self.prefix)
            self.memo_js = new_memo

        if not is_updated:
            r_list = self.getRelativeList(l)
            truncator.minify(r_list, static_l, file)

        if type == "css":
            return ["%s/generated/%s_styles.css" % (getConfig().BASE_URL, self.prefix)]
        elif type == "js":
            return ["%s/generated/%s_scripts.js" % (getConfig().BASE_URL, self.prefix)]


class Header:

    def __init__(self, name):
        self.template = None #Is set from skeletonz/server.py - markChildren

        self.styles = []
        self.scripts = []
        self.script_datas = []
        self.styles_datas = []

        self.static_script_data = []
        self.static_style_data = []

        prefix = str(self.__class__)
        prefix = prefix.replace("'>", "").split(".")[-1]

        self.truncator = Truncator(prefix)
        self.setPrefix(name)

        self.append_ajs = False

        self.sc_path = "%s/static_core" % getConfig().BASE_URL

    def setTemplate(self, obj):
        self.template = obj

    def setPrefix(self, name):
        self.truncator.setPrefix(name)

    def appendStyle(self, url):
        self.styles.append(url)

    def getStyles(self):
        return self.truncator.buildList(self.styles, self.static_style_data, "css")

    def getScripts(self):
        return self.truncator.buildList(self.scripts, self.static_script_data, "js")

    def appendScript(self, url):
        self.scripts.append(url)

    ##
    # The difference between appendScriptData and appendStaticScriptData
    # is that the first will be added dynamically while the other will be added into a file
    def _appendSciptData(self, data, wrap=None, fn_filter=None):
        fn_true = lambda x: True
        fn_filter = not fn_filter and fn_true or fn_filter
        data = wrap and wrap % data or data
        self.script_datas.append({'data': data, 'fn_filter': fn_filter})

    def appendScriptData(self, data, wrap_in_script=False, fn_filter=None):
        wrapper = wrap_in_script and '<script type="text/javascript">\n%s</script>' or '%s'
        self._appendSciptData(data, wrapper, fn_filter)

    def appendStyleData(self, data, wrap_in_style=False, fn_filter=None):
        wrapper = wrap_in_style and '<style type="text/css">\n%s</style>' or '%s'
        self._appendSciptData(data, wrapper, fn_filter)

    def appendStaticScriptData(self, data):
        self.static_script_data.append(data)

    def appendStaticStyleData(self, data):
        self.static_style_data.append(data)

    def clearScriptData(self):
        self.script_datas = []

    ##
    # Append able scripts
    #
    def appendAJS(self):
        """
        AmiJS isn't concated to make it cache-able.
        """
        self.append_ajs = True

    def appendStaticCoreScript(self, name):
        self.appendScript("%s/%s" % (self.sc_path, name))

    def appendStaticCoreStyle(self, name):
        self.appendStyle("%s/%s" % (self.sc_path, name))

    added_editor_scripts = False
    added_commander_scripts = False
    added_added_tooltip = False
    added_googie_spell = False

    def appendEditorScripts(self):
        if not self.added_editor_scripts:
            #AmiTooltip
            self.appendStaticCoreScript("/amitooltip/amitooltip.js")
            self.appendStaticCoreStyle("/amitooltip/amitooltip.css")

            #AmiBar
            self.appendStaticCoreScript("/amibar/amiformat.js")
            self.appendStaticCoreScript("/amibar/amibar.js")
            self.appendStaticCoreStyle("/amibar/amibar.css")

            self.appendStaticCoreScript('/scripts/Content.js')

            self.added_editor_scripts = True

    def appendListCommanderScripts(self):
        if not self.added_commander_scripts:
            self.appendStaticCoreStyle("/list_commander/list_commander.css")
            self.appendStaticCoreScript("/list_commander/list_commander.js")
            self.appendStaticCoreScript("/list_commander/action_bar.js")
            self.added_commander_scripts = True

    def appendTooltip(self):
        if not self.added_added_tooltip:
            self.appendStaticCoreScript("/amitooltip/amitooltip.js")
            self.appendStaticCoreStyle("/amitooltip/amitooltip.css")
            self.added_added_tooltip = True

    def appendGoogieSpell(self):
        if not self.added_googie_spell:
            self.appendStaticCoreStyle("/googiespell/googiespell.css")
            self.appendStaticCoreScript('/googiespell/googiespell.js')
            self.appendStaticCoreScript('/googiespell/cookiesupport.js')
            self.added_googie_spell = True

class AdminHeader(Header, object):

    def __init__(self, title):
        super(AdminHeader, self).__init__(converters.makeLinkAble(title))
        self.title = ''

        self.appendAJS()
        self.appendTooltip()

        self.setTitle(title)

    def setTitle(self, title):
        self.title = title

    def renderText(self):
        ns = {"header_obj": self,
              'template': self.template}
        return render("skeletonz/view/general_templates/admin_header.tmpl", ns)


class ShowHeader(Header, object):

    def __init__(self, title, name='ShowHeader'):
        super(ShowHeader, self).__init__(name)
        self.title = title

    def setTitle(self, title):
        self.title = title

    def renderText(self):
        ns = {'template': self.template,
              'title': self.title}
        return render("skeletonz/view/general_templates/show_header.tmpl", ns)



class SiteHeader(Header, object):

    def __init__(self):
        super(SiteHeader, self).__init__('SiteHeader')
        self.bodyclass = ''
        self.page_obj = None
        try:
            self.title_prefix = getConfig().TITLE_PREFIX
        except:
            self.title_prefix = ''

        try:
            self.title_suffix = getConfig().TITLE_SUFFIX
        except:
            self.title_suffix = ''

        self.edit_mode = False

        self.appendStaticCoreStyle("/styles/general.css")

        #Append init and greybox styles
        self.appendStaticCoreStyle("/greybox/gb_styles.css")
        self.appendStaticCoreScript('/scripts/general/init_normal.js')

    def setPage(self, page_obj):
        self.page_obj = page_obj

    def setEditMode(self, edit_mode):
        self.edit_mode = edit_mode

    def setBodyClass(self, bclass):
        self.bodyclass = bclass

    def renderText(self):
        ns = {"header_obj": self,
              'template': self.template,
              'page_obj': self.page_obj}
        return render("skeletonz/view/general_templates/site_header.tmpl", ns)
