from skeletonz.modules.plugin import GenericPlugin
from skeletonz.server import getFormatManager
from skeletonz.user_plugins import ListOption, TextOption

import upload
import filter

PLUGINS_FOR_EXPORT = ['UploadPlugin']

class UploadPlugin(GenericPlugin):
    NAME = "Upload"
    DESCRIPTION = "Upload files and images to your site."
    SYNTAX = [
        {'handler': 'file',
         'required_arguments': {'ident': 'The identification'},
         'optional_arguments': {}
        },

        {'handler': 'image',
         'required_arguments': {'ident': 'The identification'},
         'optional_arguments': {'linkonly': {'type': 'option', 'help': 'Should the image be displayed as an URL?'},
                                 'align': 'Where the picture should be aligned. Can be <b>left</b> or <b>right</b>.'}
        }
      ]

    def __init__(self):
        self.plugin_path = "skeletonz/plugins/upload"

        format_man = getFormatManager()
        format_man.registerSLPlugin('file', self.handleFiles)
        format_man.registerSLPlugin('image', self.handleImages)
        format_man.registerSLPlugin('personnelimage', self.handlePersonnelImages)

    def addToSiteEditTemplate(self, template, on_init):
        if on_init:
            script = "%s/static/Upload.js" % self.plugin_path
            template.getHeader().appendScript(script)

    def addToController(self, rc):
        path = "%s/static" % self.plugin_path
        rc.addStaticPath("/static_plugin/upload/", path)
        rc.root_obj.files = FileController()
        rc.root_obj.images = ImageController()

    def handleImages(self, args, edit_mode, page):
        return False, filter.wikiImages(args, edit_mode, page)

    def handlePersonnelImages(self, args, edit_mode, page):
        return False, filter.personnelWikiImages(args, edit_mode, page)

    def handleFiles(self, args, edit_mode, page):
        return False, filter.wikiFiles(args, edit_mode, page)


class FileController(upload.Upload, object):

    def __init__(self):
        super(FileController, self).__init__("files")
        self.header.setTitle("File upload")


class ImageController(upload.Upload, object):

    def __init__(self):
        super(ImageController, self).__init__("images")
        self.header.setTitle("Image upload")
