import os, sys
import smtplib
import threading

from amilib.useful import Singleton
from amilib.amiformat.amiformat import AmiFormat
import server

from amilib.amiweb.amiweb import db
from amilib.amiweb.amiweb import session


#--- Format manager ----------------------------------------------, AmiFormat
class FormatManager(Singleton,AmiFormat):
    """Frontend for AmiFormat.

    Main functions are
       registerSLPlugin: Register a single line plugin.
       registerMLPlugin: Register a multi line plugin.
    """
    def __init__(self):
        AmiFormat.__init__(self)
        self.no_plugins = AmiFormat()
        self.current_plugins = {}

    def resetPagePlugins(self):
        page_id = server.getCurrentPage()
        if self.current_plugins.has_key(page_id):
            del self.current_plugins[page_id]

    def htmlFormat(self, text, edit_mode, wrap_in_p=True, page=None):
        page_id = server.getCurrentPage()

        if not self.current_plugins.has_key(page_id):
            self.current_plugins[page_id] = {}

        old_edit_mode = None
        if hasattr(self, 'edit_mode'):
            old_edit_mode = self.edit_mode

        self.edit_mode = edit_mode
        self.page = page

        result = AmiFormat.htmlFormat(self, text)

        if not wrap_in_p:
            result = self._removeFrontP(result)

        if old_edit_mode:
            self.edit_mode = old_edit_mode
        return result

    def noPluginFormat(self, text, wrap_in_p=True):
        """Format without support for plugins.

        Useful for input that comes from users that aren't a part of the system.
        For example blog comments.
        """
        result = self.no_plugins.htmlFormat(text)
        if not wrap_in_p:
            result = self._removeFrontP(result)
        return result

    def getAllPluginArguments(self, tag):
        """Returns all the parsed plugins that have tag.

        Is restarted every time htmlFormat is called.
        The result is a list looking like this:
            [{'tag': 'ident1'}, ..., {'tag': 'identN'}]
        """
        page_id = server.getCurrentPage()
        return self.current_plugins[page_id].get(tag) or {}

    def getPluginArguments(self, tag, ident):
        """Returns the arguments of a plugin with tag=ident.

        For example if we have plugin [file=Foo, show_as=Bar],
        the we could call getPluginArguments('file', 'Foo') to get
        {'file': 'Foo', 'show_as': 'Bar'}
        """
        all = self.getAllPluginArguments(tag)
        for args in all:
            if args[tag] == ident:
                return args
        return None

    def _removeFrontP(self, text):
        text = text.replace('\n<p>\n', '', 1)
        text = text.replace('\n</p>\n', '', 1)
        return text

    def _registerPluginArgs(self, tag, args):
        page_id = server.getCurrentPage()
        if self.current_plugins[page_id].has_key(tag):
            self.current_plugins[page_id][tag].append(args)
        else:
            self.current_plugins[page_id][tag] = [args]

    #--- Callbacks ----------------------------------------------
    def pluginSLParsed(self, tag, args, edit_mode=None):
        if edit_mode == None:
            edit_mode = self.edit_mode
        if self.isSLPlugin(tag):
            self._registerPluginArgs(tag, args)
            return self.sl_plugins[tag](args, edit_mode, self.page)

    def pluginMLParsed(self, tag, args, edit_mode=None):
        if edit_mode == None:
            edit_mode = self.edit_mode
        if self.isMLPlugin(tag):
            self._registerPluginArgs(tag, args)
            return self.ml_plugins[tag](args, edit_mode, self.page)

    #--- Helpers ----------------------------------------------

    def imageEditModeLink(self, image_plugin, edit_mode):
        """Return a rendered image plugin where the image is shown in normal mode,
        while it is hidden by an image upload icon in edit mode.

        Useful when one combines plugins, such as:
            [file=Test, show_as=My image].
        """
        if image_plugin['plugin_type'] == 'image':
            if edit_mode:
                image_plugin['plugin_args']['linkonly'] = True
            else:
                if image_plugin['plugin_args'].has_key('linkonly'):
                    del image_plugin['plugin_args']['linkonly']

            block, output = self.pluginSLParsed('image', image_plugin['plugin_args'], edit_mode)
            return output
        else:
            return None

    def imageAlwaysLink(self, image_plugin, edit_mode):
        """Return a rendered image plugin where only the image URL is shown in normal mode,
        while it is hidden by an image upload icon in edit mode.

        Useful when one combine plugins, such as:
            [gb_image=[image=Test small], link=[image=Test big]]
        """
        if image_plugin['plugin_type'] == 'image':
            image_plugin['plugin_args']['linkonly'] = True
            block, output = self.pluginSLParsed('image', image_plugin['plugin_args'], edit_mode)
            return output
        else:
            return None



#--- RSS manager ----------------------------------------------
class RSSManager(Singleton):
    def __init__(self):
         self.rss_dir = "dynamic_dirs/rss"

    def publish(self, file, rss2_obj):
        rss2_obj.write_xml(open("%s/%s" % (self.rss_dir, file), "w"), 'utf8')

    def remove(self, file):
        os.remove("%s/%s" % (self.rss_dir, file))


#--- Email manager ----------------------------------------------
ERROR_STR = """Could not delivery mail to: %s.

Server said: %s
%s
"""

MSG_STR = """To: %s
From: %s
Subject: %s

%s
"""

class MailManager(Singleton):

    def __init__(self):
        self.setup = False

    def setUpIfNeeded(self):
        if self.setup:
            return

        c = server.getConfig()
        if hasattr(c, 'SMTP_SERVER'):
            self.server = c.SMTP_SERVER
            self.auth_required = False
            if c.SMTP_AUTH_REQUIRED:
                self.auth_required = True
                if hasattr(c, 'SMTP_USER') and hasattr(c, 'SMTP_PASSWORD'):
                    self.user = c.SMTP_USER
                    self.pwd = c.SMTP_PASSWORD
                    self.setup = True
                else:
                    raise Exception("When SMTP_AUTH_REQUIRED is set to True, then SMTP_USER and SMTP_PASSWORD must be set.")
        else:
            raise Exception("SMTP_SERVER must be set in general_config.py.")

    def sendEmail(self, sender, recipients, subject, message):
        def _sendEmail():
            self.setUpIfNeeded()
            msg = MSG_STR  % (", ".join(recipients), sender, subject, message)
            session = smtplib.SMTP(self.server)
            if self.auth_required:
                session.login(self.user, self.pwd)
            smtpresult = session.sendmail(sender, recipients, msg)

            if smtpresult:
                err = []
                for recip in smtpresult.keys():
                    err.append(ERROR_STR % (recip, smtpresult[recip][0], smtpresult[recip][1]))
                raise smtplib.SMTPException, "".join(err)

        t = threading.Thread(target=_sendEmail)
        t.start()
