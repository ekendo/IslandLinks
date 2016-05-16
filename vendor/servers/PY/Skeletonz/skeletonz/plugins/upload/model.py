import os
import re

from amilib.amiweb.amiweb import db, session
from amilib import json

from skeletonz.server import getConfig
from skeletonz.model.CMSModel import PageDeco

from amilib.useful import partial

UPLOAD_DIR = "dynamic_dirs/uploads"

class FileDeco:

    def getFilename(self):
        static_dir = "%s/uploads" % getConfig().BASE_URL
        return "%s/%s" % (static_dir, self.filename)

    def toJSON(self):
        ident = self.ident
        if len(ident) > 30:
            ident = '%s...' % ident[0:30]
        file_name = self.filename
        if len(file_name) > 30:
            file_name = '%s...' % file_name[0:30]

        d = {'id': self.id,
            'content': "%s [%s]" % (ident, file_name)}
        return json.write(d)


class Files:

    def getFileById(self, id):
        return db().select("file", id=id, as_one=True, obj_deco=FileDeco)

    def getFileByIdent(self, ident, ftype, parent_id):
        q = partial(db().select, "file", ident=ident, type=ftype, as_one=True, obj_deco=FileDeco)
        if parent_id:
            return q(parent_id=parent_id)
        return q()

    def _getFullPath(self):
        dir_full = os.path.abspath("%s" % (UPLOAD_DIR))
        return dir_full

    def notCreated(self, filename):
        return self.countFilesWithFilename(filename) == 0

    def countFilesWithFilename(self, filename):
        return db().selectCount("file", filename=filename)

    def newFilename(self, old_fn):
        p = re.compile('(.*?)\.(.*)')
        a = p.match(old_fn)

        is_ok = False
        count = self.countFilesWithFilename(old_fn) + 1
        while not is_ok:
            count += 1

            if a == None:
                new_fn = "%s%i" % (old_fn, count)
            else:
                new_fn = "%s%i.%s" % (a.groups()[0], count, a.groups()[1])

            if self.notCreated(new_fn):
                is_ok = True
        return new_fn

    def uploadFile(self, ident, filename, ftype, data, parent_id=None):
        os_filename = os.path.abspath("%s/%s" % (self._getFullPath(), filename))
        fp = open(os_filename, "wb")
        fp.write(data)
        fp.close()

        if not parent_id:
            parent_id = session()['current_page_id']

        if self.notCreated(filename):
            id = db().insert("file", ident=ident, filename=filename, type=ftype, parent_id=parent_id)
        else:
            db().delete('file', filename=filename, type=ftype)
            id = db().insert("file", ident=ident, filename=filename, type=ftype, parent_id=parent_id)
        return self.getFileById(id)

    def uploadFileEdit(self, id, filename, ftype, data):
        file = self.getFileById(id)
        self.deleteFileById(id)
        return self.uploadFile(file.ident, filename, ftype, data, file.parent_id)

    def deleteFileById(self, id):
        file = self.getFileById(id)
        ident = db().select("file", id=id, cols=["ident"], as_one=True).ident
        try:
            os.remove("%s/%s" % (self._getFullPath(), file.filename))
        except:
            pass
        db().delete("file", id=id)
        return ident

    def getAllFiles(self):
        return db().select("file", obj_deco=FileDeco, order_by="id")

    def getAllPagesWhereFileIsUsed(self, id):
        #ERROR: image - should also be file!!
        file_obj = self.getFileById(id)
        pages = db().select("page", "content LIKE '%[image=" + file_obj.ident + "]%'", obj_deco=PageDeco)
        return pages

    def getAllFilesWithNoParent(self):
        return db().select("file", parent_id=None, order_by='ident', obj_deco=FileDeco)

    def getAllFilesOnPage(self, page_obj):
        return db().select("file", parent_id=page_obj.id, order_by='ident', obj_deco=FileDeco)

    def getAllFilesByTextScan(self, content):
        if content == None:
            return []

        WIKI_UPLOAD = re.compile('\[file=(.*?)(,.*?)?\]', re.IGNORECASE)
        WIKI_IMAGES = re.compile('\[image=(.*?)(,.*?)?\]', re.IGNORECASE)

        files = []
        for m in WIKI_UPLOAD.finditer(content):
            f = self.getFileByIdent(m.groups()[0], "skfile", None)
            if f != None:
                files.append(f)

        for m in WIKI_IMAGES.finditer(content):
            f = self.getFileByIdent(m.groups()[0], "skimg", None)
            if f != None:
                files.append(f)
        return files
Files = Files()
