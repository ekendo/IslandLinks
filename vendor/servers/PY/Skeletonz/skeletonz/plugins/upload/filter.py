import re
import types

from amilib.template import render
from skeletonz.mylib.converters import makeLinkAble
from skeletonz.mylib import html_helpers
from skeletonz.server import getConfig, getFormatManager, getCurrentPage

import model

class Upload:
    """
    The templates should have $ident and $filename
    """

    def __init__(self, type, args, get_obj, f_type):
        #get_obj is a function that should return a boolean
        self.type = type
        self.args = args
        self.get_obj = get_obj
        self.f_type = f_type
        self.current_page = getCurrentPage()

    def setUploadNew(self, template):
        self.tmpl_upload_new = template

    def setUploadEdit(self, template):
        self.tmpl_upload_edit = template

    def setView(self, template):
        self.tmpl_view = template

    def setViewNotCreated(self, template):
        self.tmpl_view_not_created = template

    def appendFilter(self, edit_mode, **kv):
        #Extra arguments can be sent and they will be visible from the templates
        ident = self.args[self.type]
        #Link
        link = self.args.get("link", None)
        #Hidden
        hidden = self.args.get("hidden", None)

        ident_id = makeLinkAble(ident)

        cur_page_id = self.current_page
        if self.args.has_key('global'):
            cur_page_id = None

        obj = self.get_obj(ident, self.f_type, cur_page_id)

        #Set template namespace
        ns = {'BASE_URL': getConfig().BASE_URL,
              'ident_id': ident_id,
              'ident': ident}

        try: ns['obj'] = obj
        except: pass

        #Append extra
        for k in kv.keys():
            ns[k] = kv[k]

        if link:
            ns['LINK_START'] = '<a href="%s">' % (link)
            ns['LINK_END'] = '</a>'
        else:
            ns['LINK_START'] = ''
            ns['LINK_END'] = ''

        if self.args.has_key('align'):
            ns['LINK_START'] = '<div style="float: %s">%s' % (self.args['align'], ns['LINK_START'])
            ns['LINK_END'] = '%s</div>' % (ns['LINK_END'])

        if edit_mode == True:
            if obj == None:
                replacer = render(self.tmpl_upload_new, ns, True)
            else:
                replacer = render(self.tmpl_upload_edit, ns, True)
        else:
            if obj == None:
                replacer = render(self.tmpl_view_not_created, ns, True)
            else:
                if link:
                    if link == "linkonly":
                        replacer = render('$obj.getFilename()', ns, True)
                    else:
                        replacer = render(self.tmpl_view, ns, True)
                elif hidden:
                    replacer = ""
                else:
                    replacer = render(self.tmpl_view, ns, True)
        return "%s" % replacer


def wikiFiles(args, edit_mode, current_page):
    up = Upload("file", args, model.Files.getFileByIdent, "skfile")

    btn_upload_new = html_helpers.createActionLink('Upload file', 'static_plugin/upload/file_add.png', "EC_Upload.showUploadWindowIdent('files', this.id, 'skfile')", tooltip_inner=True, id=args['file'])

    up.setUploadNew("""<span id="skfile_$ident_id" class="skfile"><span class="CMS_NotCreated">$normal_view</span> $edit_view \
  %s</span>""" % btn_upload_new)

    btn_edit = html_helpers.createActionLink('Upload new file', 'static_plugin/upload/file_edit.png', "EC_Upload.showUploadWindowId('files', '$obj.id', 'skfile')", tooltip_inner=True)
    btn_del = html_helpers.createActionLink('Delete file', 'static_plugin/upload/file_delete.png', "EC_Upload.deleteFile(this.parentNode, 'files', '$obj.id', 'skfile')", "CMS_link CMS_critical_link", tooltip_inner=True)

    up.setUploadEdit("""<span id="skfile_$ident_id" class="skfile"><a href="$obj.getFilename()">$normal_view</a> $edit_view \
  %s %s</span>""" % (btn_edit, btn_del))

    up.setView("""<a href="$obj.getFilename()">$normal_view</a>""")
    up.setViewNotCreated("""<span class="CMS_NotCreated">$normal_view</span> $edit_view""")

    normal_view = re.sub('\s*!\d+', '', args['file'])
    edit_view = ''

    if args.has_key('show_as'):
        show_as = args['show_as']
        if type(show_as) == types.DictType:
            if show_as['plugin_type'] == 'image':
                if edit_mode:
                    normal_view = ''
                    edit_view = getFormatManager().imageEditModeLink(show_as, edit_mode)
                else:
                    normal_view = getFormatManager().imageEditModeLink(show_as, edit_mode)
                    edit_view = ''
            else:
                print 'ERROR: Only images are supported in [page=..., show_as=...]'
        else:
            normal_view = show_as

    return up.appendFilter(edit_mode, normal_view=normal_view, edit_view=edit_view)


def genericFilterWikiImages(r_type, args, tmpl_upload_new, tmpl_upload_edit, tmpl_view, tmpl_not_created, type):
    up = Upload(r_type, args, model.Files.getFileByIdent, type)
    up.setUploadNew(tmpl_upload_new)
    up.setUploadEdit(tmpl_upload_edit)
    up.setView(tmpl_view)
    up.setViewNotCreated(tmpl_not_created)
    return up


def wikiImages(args, edit_mode, current_page):
    #Put it in a <div ...> because we need to edit them. ident should be unique.
    linkonly = "0"
    if args.has_key('linkonly'):
        linkonly = "1"

    btn_upload_new = html_helpers.createActionLink('Upload image', 'static_plugin/upload/image_add.png', "EC_Upload.showUploadWindowIdent('images', this.id, 'skimg', %s)" % linkonly, tooltip_inner=True, id=args['image'])

    tmpl_upload_new = """<span id="skimg_$ident_id" class="skimg"><span class="CMS_NotCreated">$ident</span> \
  %s</span>""" % btn_upload_new

    btn_edit = html_helpers.createActionLink('Upload new image', 'static_plugin/upload/image_edit.png', "EC_Upload.showUploadWindowId('images', '$obj.id', 'skimg', %s)" % linkonly, tooltip_inner=True)
    btn_del = html_helpers.createActionLink('Delete image', 'static_plugin/upload/image_delete.png', "EC_Upload.deleteFile(this.parentNode, 'images', '$obj.id', 'skimg')", "CMS_link CMS_critical_link", tooltip_inner=True)

    img_action = "%s %s" % (btn_edit, btn_del)

    if args.has_key('linkonly'):
        tmpl_upload_edit = """<span id="skimg_$ident_id" class="skimg">$LINK_START<img src="static_plugin/upload/image_link.png" alt="$ident" />$LINK_END %s</span>""" % img_action
    else:
        tmpl_upload_edit = """<span id="skimg_$ident_id" class="skimg">$LINK_START<img src="$obj.getFilename()" alt="$ident" />$LINK_END %s</span>""" % img_action

    if args.has_key('linkonly'):
        tmpl_view = """$obj.getFilename()"""
        tmpl_not_created = "#"
    else:
        tmpl_view = """$LINK_START<img src="$obj.getFilename()" alt="$ident" />$LINK_END"""
        tmpl_not_created = """<span class="CMS_NotCreated">Image $ident not uploaded</span>"""

    up = genericFilterWikiImages("image", args, tmpl_upload_new, tmpl_upload_edit, tmpl_view, tmpl_not_created, "skimg")
    return up.appendFilter(edit_mode)


def personnelWikiImages(args, edit_mode, current_page):
    """
    Images used for the personnel table
    """
    btn_upload_new = html_helpers.createActionLink('Upload image', 'static_plugin/upload/image_add.png', "EC_Upload.showUploadWindowIdent('images', this.id, 'personnelimage')", tooltip_inner=True, id=args['personnelimage'])

    tmpl_upload_new = """<span id="personnelimage_$ident_id" class="personnelimage"><span class="CMS_NotCreated">$ident</span> <br /> %s</span>""" % btn_upload_new

    btn_edit = html_helpers.createActionLink('Upload new image', 'static_plugin/upload/image_edit.png', "EC_Upload.showUploadWindowId('images', '$obj.id', 'personnelimage')", tooltip_inner=True)
    btn_del = html_helpers.createActionLink('Delete image', 'static_plugin/upload/image_delete.png', "EC_Upload.deleteFile(this.parentNode, 'images', '$obj.id', 'personnelimage')", "CMS_link CMS_critical_link", tooltip_inner=True)

    tmpl_upload_edit = """<span id="personnelimage_$ident_id" class="personnelimage"><img src="$obj.getFilename()" alt="$ident" style="border: 2px solid black;" /> <br />\
  %s %s</span>""" % (btn_edit, btn_del)

    tmpl_view = """<img src="$obj.getFilename()" alt="$ident" style="border: 2px solid black;" />"""

    tmpl_not_created = """<span>Not uploaded</span>"""
    up = genericFilterWikiImages("personnelimage", args, tmpl_upload_new, tmpl_upload_edit, tmpl_view, tmpl_not_created, "personnelimage")
    return up.appendFilter(edit_mode)
