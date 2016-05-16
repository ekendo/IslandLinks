import re
import urllib
import types

from skeletonz.mylib.converters import makeLinkAble
from skeletonz.mylib import html_helpers
from amilib.template import render
from amilib.useful import RND
from skeletonz.server import getConfig, getFormatManager


##
# Wiki words
#
def wikiWords(args, edit_mode, current_page):
    from skeletonz.model import CMSModel as Model
    from skeletonz.Site import Users
    import skeletonz.model.visit as visit

    #Set the view
    btn_create_page = html_helpers.createActionLink('', 'static_plugin/wikiplugin/page_add.png')
    btn_create_page = '<a href="siteedit/pageCreate?parent_id=$parent_id&$encode($ident)">%s</a>' % (html_helpers.createToolTip("Create page", btn_create_page, ""))

    btn_delete_page = html_helpers.createActionLink('Delete page', 'static_plugin/wikiplugin/page_delete.png', 'return EC_Pages.remove(this, $obj.id)', 'CMS_link CMS_critical_link', tooltip_inner=True, confirm="Are you sure you want to delete this page?")

    tmpl_new = """<span><span class="CMS_NotCreated">$normal_view</span> $edit_view %s</span>""" % btn_create_page
    tmpl_edit = """<span><a href="$BASE_URL/$obj.getFullLink()">$normal_view</a> $edit_view %s</span>""" % btn_delete_page
    tmpl_view = """<a href="$BASE_URL/$obj.getFullLink()">$normal_view</a>"""
    tmpl_view_not_created = """<span class="CMS_NotCreated">$normal_view</span>"""

    if args.has_key('page'):
        ident = args['page']
        if args.has_key('static') or args.has_key('global'):
            obj = Model.Pages.getPageGlobal(ident)
        else:
            obj = Model.Pages.getPage(ident, current_page.id)

        site_edit = ''
        if edit_mode:
            site_edit = '/siteedit'

        def encode(x):
            return urllib.urlencode({'name': x})

        normal_view = ident
        edit_view = ''

        if args.has_key('show_as'):
            show_as = args['show_as']
            if type(show_as) == types.DictType:
                if show_as['plugin_type'] == 'image':
                    if edit_mode:
                        normal_view = ident
                        edit_view = getFormatManager().imageEditModeLink(show_as, edit_mode)
                    else:
                        normal_view = getFormatManager().imageEditModeLink(show_as, edit_mode)
                        edit_view = ''
                else:
                    print 'ERROR: Only images are supported in [page=..., show_as=...]'
            else:
                normal_view = show_as

        ns = {
          'BASE_URL': "%s%s" % (getConfig().BASE_URL, site_edit),
          'ident': ident,
          'normal_view': normal_view,
          'edit_view': edit_view,
          'obj': obj,
          'parent_id': current_page and current_page.id or 1,
          'encode': encode
        }

        ns['normal_view'] = re.sub('\s*!\d', '', ns['normal_view'])

        if edit_mode == True:
            #Check if the obj if the page is created, switch on the views
            if obj == None:
                replacer = render(tmpl_new, ns, True)
            else:
                replacer = render(tmpl_edit, ns, True)
        else:
            #Check if the obj if the page is created, switch on the views
            if obj == None or obj.hidden == True:
                replacer = render(tmpl_view_not_created, ns, True)
            else:
                replacer = render(tmpl_view, ns, True)

    return replacer


##
# Wiki page link
#
def wikiPageLink(args, edit_mode, current_page):
    """
    A href to a page in the system
    """
    ident = args['pagelink']
    section = args.get('section', None)
    result = getPageLinkHTML(ident, edit_mode, section, False)

    ns = {
        'normal_view': ident,
        'edit_view': ''
    }

    if args.has_key('show_as'):
        show_as = args['show_as']
        if type(show_as) == types.DictType:
            if show_as['plugin_type'] == 'image':
                if edit_mode:
                    ns['normal_view'] = ''
                    ns['edit_view'] = getFormatManager().imageEditModeLink(show_as, edit_mode)
                else:
                    ns['normal_view'] = getFormatManager().imageEditModeLink(show_as, edit_mode)
                    ns['edit_view'] = ''
            else:
                print 'ERROR: Only images are supported in [pagelink=..., show_as=...]'
        else:
            ns['normal_view'] = show_as
    return render(result, ns, True)

def getPageLinkHTML(ident, edit_mode, section, only_form):
    from skeletonz.model import CMSModel as Model
    from skeletonz.Site import Users
    import skeletonz.model.visit as visit

    page_link = Model.PageLinks.getPageLinkByIdent(ident)
    #If it is None, then it's not created yet!
    if page_link == None:
        page_link = Model.PageLinks.createPageLink(ident)

    url = getConfig().BASE_URL
    if edit_mode:
        url = '%s/siteedit' % getConfig().BASE_URL

    js_update = "EC_Pagelink.update"

    if edit_mode:
        site_map = visit.getSitemap()
        form = visit.createOptions(page_link.getPage(), site_map, page_link.id, js_update)
        if only_form:
            return r'%s' % visit.createOptions(page_link.getPage(), site_map, page_link.id, js_update)
        else:
            if page_link.getPage() == None:
                return r'<span class="page_link_action"><a href="#">$normal_view</a></span> $edit_view %s' %\
                        (form)
            else:
                p_url = page_link.getPage().getFullLink()
                if section != None:
                    p_url = "%s#%s" % (p_url, section)

                return r'<span class="page_link_action"><a href="%s/%s">$normal_view</a></span> $edit_view %s' %\
                    (url, p_url, visit.createOptions(page_link.getPage(), site_map, page_link.id, js_update))
    else:
        if page_link.getPage() == None:
            #The page still does not have a reference
            return r'<span class="CMS_NotCreated">$normal_view</span>'
        else:
            page_link = page_link.getPage().getFullLink()
            if section is None:
                return r'<a href="%s/%s">$normal_view</a>' % (url, page_link)
            else:
                return r'<a href="%s/%s#%s">$normal_view</a>' % (url, page_link, section)
