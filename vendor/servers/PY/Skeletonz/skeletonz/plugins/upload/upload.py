import urllib

from skeletonz.modules.template import PluginTemplate
from skeletonz.model import CMSModel

import filter
from skeletonz.mylib.converters import makeLinkAble
from skeletonz.mylib.amicache import AmiCache
from amilib.template import render
from skeletonz.server import getConfig, getFormatManager

from amilib.amiweb import amiweb

import model

class Upload:

    def __init__(self, handler):
        """If your mapping is to /files/, then handler='files'"""
        template = PluginTemplate()
        self.header = template.getHeader()
        self.footer = template.getFooter()
        self.handler = handler

    @amiweb.expose
    def showUpload(self, ftype, id=0, linkonly=0):
        file = model.Files.getFileById(id)

        ns = {'site': self,
              'BASE_URL': getConfig().BASE_URL,
              'file': file,
              'page_id': amiweb.session()['current_page_id'],
              'ftype': ftype,
              'linkonly': linkonly}
        return render("skeletonz/plugins/upload/view/upload.tmpl", ns)

    @amiweb.expose
    def deleteFile(self, file_id, ftype, linkonly=0):
        ident = model.Files.deleteFileById(file_id)

        page_obj = CMSModel.Pages.getPageById(amiweb.session()['current_page_id'])
        AmiCache.expireCurrentPage()
        return self.getEditHTML(ftype, ident, linkonly)

    @amiweb.expose
    def fixFilename(self, action, new_name):
        ns = amiweb.session()['upload_ns']
        if action == 'change':
            if model.Files.countFilesWithFilename(new_name):
                ns['filename'] = filename
                ns['new_filename'] = model.Files.newFilename(filename)
                return render("skeletonz/plugins/upload/view/upload_already_found.tmpl", ns)

            ns['filename'] = new_name
        #Else let is stay the same

        del amiweb.session()['upload_ns']
        return self.uploadComplete(ns)

    @amiweb.expose
    def uploadFile(self, file, ident, ftype, id=0, linkonly=0):
        bdata = ""

        for l in file.file.readlines():
            bdata += l

        filename = file.filename.split("\\")
        filename = filename[-1:][0]

        ns = {'site': self,
              'BASE_URL': getConfig().BASE_URL,
              'ident': ident,
              'id': id,
              'filename': filename,
              'ident_id': makeLinkAble(ident),
              'linkonly': linkonly,
              'ftype': ftype}

        ns['data'] = bdata

        if model.Files.countFilesWithFilename(filename):
            ns['new_filename'] = model.Files.newFilename(filename)
            amiweb.session()['upload_ns'] = ns
            return render("skeletonz/plugins/upload/view/upload_already_found.tmpl", ns)

        return self.uploadComplete(ns)

    def uploadComplete(self, ns):
        id = ns['id']
        bdata = ns['data']
        ftype = ns['ftype']
        ident = ns['ident']
        linkonly = ns['linkonly']
        filename = ns['filename']

        if id != 0:
            file_obj = model.Files.uploadFileEdit(id, filename, ftype, bdata)
        else:
            file_obj = model.Files.uploadFile(ident, filename, ftype, bdata)

        ns['filename'] = file_obj.getFilename()

        new_html = self.getEditHTML(ftype, ident, linkonly)
        new_html = new_html.replace("'", "\\'")
        ns['new_html'] = new_html

        AmiCache.expireCurrentPage()
        return render("skeletonz/plugins/upload/view/upload_complete.tmpl", ns)

    @amiweb.expose
    def getEditHTML(self, type, ident, linkonly=0):
        if type == "skfile":
            args = getFormatManager().getPluginArguments('file', ident)
            return filter.wikiFiles(args, True, None)
        if type == "skimg":
            args = getFormatManager().getPluginArguments('image', ident)
            return filter.wikiImages(args, True, None)
        if type == "personnelimage":
            args = getFormatManager().getPluginArguments('personnelimage', ident)
            return filter.personnelWikiImages(args, True, None)
