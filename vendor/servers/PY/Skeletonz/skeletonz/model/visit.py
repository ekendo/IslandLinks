import sys, os
sys.path.insert(0, os.path.abspath(""))

import CMSModel as Model
import UserModel

class SiteMapVisitor:

    def visitMenu(self, m_obj, pages):
        v_pages = []

        for page in pages:
            if page and not page.parent_id:
                v_pages.append( page.accept(self) )

        return [m_obj, v_pages]

    def visitPage(self, p_obj, children):
        v_child = []
        if children != None:
            for child in children:
                v_child.append( child.accept(self) )
        return [p_obj, v_child]


def getSitemap():
    menus = Model.Menus.getAllMenus()

    visitor = SiteMapVisitor()

    list_repr = []
    for menu in menus:
        list_repr.append( menu.accept(visitor) )
    return list_repr

def getPremissions():
    premissions = {}
    premissions['Users'] = []
    premissions['Groups'] = []

    for user in UserModel.Users.getAllUsers():
        premissions['Users'].append(user.username)

    for group in UserModel.Groups.getAllGroups():
        premissions['Groups'].append(group.name)

    return premissions

##
# HTML functions
#
def printPage(current_page, page_item, ident):
    result = []
    for under_page in page_item:
        title = under_page[0].name or under_page[0].title
        if title and len(title) >= 20:
            title = "%s..." % title[:20]
        if current_page != None and current_page.id == under_page[0].id:
            result.append('<option value="%s" selected="selected">%s%s</option>' % (under_page[0].id, ident, title))
        else:
            result.append('<option value="%s">%s%s</option>' % (under_page[0].id, ident, title))
        if under_page[1] != []:
            result.append(printPage(current_page, under_page[1], "&nbsp;&nbsp;%s" % ident))
    return "".join(result)

def createOptions(current_page, site_map, item_id, js_update, null_item=None):
    result = ['<select onchange="%s(this, %s)">' % (js_update, item_id)]
    if null_item:
        result.append('<option value="null" selected="selected">%s</option>' % null_item)
    elif null_item == False:
        pass
    else:
        result.append('<option value="null">Choose a page</option>')

    #sitemap looks like [[menu, [_under_]], ...]
    for item in site_map:
        result.append(printPage(current_page, item[1], "&nbsp;"))
    result.append('</select>')
    return "".join(result)
