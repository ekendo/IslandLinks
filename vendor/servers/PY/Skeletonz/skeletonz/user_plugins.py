import os, sys

from skeletonz.mylib.errors import ppError
from amilib import json


#--- Plugin manager ----------------------------------------------
class PluginManager:

    def __init__(self):
        self.clearPlugins()
        self.importPlugins()

    def getPlugins(self):
        return self.plugins.values()

    def listAvailablePlugins(self):
        p_names = self.plugins.keys()

        l = []
        for name in p_names:
            l.append({
              'id': name,
              'name': self.getPluginName(name),
              'description': self.getPluginDescription(name),
              'syntax': self.getPluginSyntax(name),
              'has_options': self.getPluginOptions(name),
              'is_skeletonz_plugin': self.plugins[name]['type'] == 'skeletonz',
              'display_in_admin': self.displayInAdmin(name)
            })

        #Sort after name
        def mysort(x, y):
            if x['name'] > y['name']:
                return 1
            elif x['name'] < y['name']:
                return -1
            else:
                return 0
        l.sort(mysort)
        return l

    def getModule(self, class_name):
        return self.plugins[class_name]['module']

    def getPluginOptions(self, class_name):
        return getattr(self.getModule(class_name), 'PLUGIN_OPTIONS', None)

    def getPluginDescription(self, class_name):
        desc = getattr(self.getModule(class_name), 'DESCRIPTION', '')
        return json.write(desc)

    def getPluginName(self, class_name):
        name = getattr(self.getModule(class_name), 'NAME', class_name)
        return json.write(name)

    def displayInAdmin(self, class_name):
        return getattr(self.getModule(class_name), 'DISPLAY_IN_ADMIN', True)

    def getPluginSyntax(self, class_name):
        syn = getattr(self.getModule(class_name), 'SYNTAX', None)
        if syn:
            return json.write(syn)
        else:
            return None

    def importPlugins(self):
        self.importPluginsFromDir('site_plugins', 'site_plugins')
        self.importPluginsFromDir('skeletonz/plugins', 'skeletonz')

    def importPluginsFromDir(self, plugin_dir, type):
        """Check the plugin directory and automatically try to load all available plugins"""
        plugin_folders = [folder for folder in os.listdir(plugin_dir) \
                                if (os.path.isdir(os.path.join(plugin_dir, folder)) \
                                and os.path.isfile(os.path.join(plugin_dir, folder, 'plugin.py')))]

        for p_str in plugin_folders:
            try:
                exec "import %s.%s.plugin as m" % (plugin_dir.replace("/", "."), p_str)
            except Exception:
                ppError("Plugin import error", "'%s' could not be imported:" % p_str)
            try:
                for class_name in m.PLUGINS_FOR_EXPORT:
                    self.plugins[class_name] = {
                        'module': getattr(m, class_name)(),
                        'type': type,
                        'plugin_dir': "%s" % (p_str)}

                    #print "'%s' imported as a plugin" % str(class_name)
            except Exception:
                ppError("Plugin error", "'%s' is not a valid plugin:" % p_str)

    def clearPlugins(self):
        self.plugins = {}


##
# Plugins options
#
class PluginOption:

    def __init__(self, scope, name, text=None):
        self.scope = scope
        self.name = name
        self.text = text
        if not text:
            self.text = self.name # If no text is provided, name is used as a default
        self.load()

    def save(self):
        from server import plugin_configurator
        plugin_configurator.set("Options_%s" % self.scope, self.name, self.value)

    def load(self):
        from server import plugin_configurator
        self.value = plugin_configurator.get("Options_%s" % self.scope, self.name)

    def javascriptInput(self):
        html = """<input type="text" id="%s"/>""" % self.name
        return html

    def javascriptRetrieve(self):
        script = """getElement('%s').value""" % self.name
        return script

    def getValue(self):
        return self.value

    def getDefault(self):
        raise NotImplementedError()


class ListOption(PluginOption):

    def __init__(self, scope, name, list_values, text=None, default=""):
        self.list_values = list_values
        self.default = default
        PluginOption.__init__(self, scope, name, text)

    def javascriptInput(self):
        list_options = []
        previous_value = self.value and self.value or self.default
        for val in self.list_values:
            selected = (str(val)==str(previous_value)) and "selected" or ""
            list_options.append("""<option value="%s" %s>%s</option>""" % (val, selected, val))
        options = "".join(list_options)
        html = """<dl><dt>%s:</dt><dd><select id="%s">%s</select></dd></dl>
                """ % (self.text, self.name, options)
        return html

    def getDefault(self):
        return self.default


class TextOption(PluginOption):

    def __init__(self, scope, name, text=None, default=""):
        self.default=default
        PluginOption.__init__(self, scope, name, text)

    def javascriptInput(self):
        html = """<dl><dt>%s:</dt><dd><input type="input" id="%s" value="%s" class="edit_input"/></dd></dl>
                """ % (self.text, self.name, self.value and self.value or self.default)
        return html

    def getDefault(self):
        return self.default
