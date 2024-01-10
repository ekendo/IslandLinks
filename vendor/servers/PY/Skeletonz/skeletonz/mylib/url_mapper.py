import urllib
from skeletonz.mylib import converters
from skeletonz.model import CMSModel
from amilib.amiweb import amiweb

page_memo = {}

def expirePagePath(path):
    key1 = '/siteedit/%s' % path
    if page_memo.has_key(key1):
        del page_memo[key1]

    key2 = '/%s' % path
    if page_memo.has_key(key2):
        del page_memo[key2]

def expirePageMemo():
    global page_memo
    page_memo = {}

def findChildPage(page_list):
    """Gets a list of page names. Will return the child to the left.

    If page_list looks like [PageName1, PageName2, ..., PageNameN], then PageNameN object will be returned. If some of the pages arent found None will be returned.
    """
    parent_id = None
    page = None

    for page_name in page_list:
        page_name = urllib.url2pathname(page_name)

        if page_name != '':
            page = CMSModel.Pages.getPage(page_name, parent_id)

            if not page:
                return None

            parent_id = page.id

    return page


def findPageFromPathInfo(path_info):
    path = path_info.replace("//", "/")

    #Take siteedit into the account
    path = path.replace("/siteedit/", "")
    path = path.replace('_', ' ')
    path = path.split("/")

    page = findChildPage(path)
    return page


def mapToName(rc, environ, start_response):
    #Check out to see if page_id is set
    print "[url_mapper] mapToName: processing queryString I guess,...<"
    qs = environ['QUERY_STRING']
    print qs.split("=")[1]
    print "><br>"
    
    if qs != '':
        if qs.find("page_id") != -1:
            body = rc.dispatcher.root_obj.index(qs.split("=")[1])
            print "[urlMapper] mapToName: before the serverBody<br>"
            return rc.dispatcher.serveBody(body, start_response)

    #Try to find the page
    path_info = environ["PATH_INFO"]

    siteedit = False
    if path_info.find("/siteedit/") != -1:
        siteedit = True

    page = page_memo.get(path_info, None)

    if not page:
        page = findPageFromPathInfo(path_info)
        page_memo[path_info] = page

    def servePage(page_inner):
        if siteedit:
            rc.dispatcher.root_obj.siteedit.filter()
            body = rc.dispatcher.root_obj.siteedit.index(page_inner.id)
        else:
            body = rc.dispatcher.root_obj.index(page_inner.id)
        return rc.dispatcher.serveBody(body, start_response)

    if page:
        try:
            return servePage(page)
        except amiweb.HTTPNotFound, (e):
            #Maybe the page path is being updated
            #here we manually update it to see if that helps
            page = findPageFromPathInfo(path_info)
            page_memo[path_info] = page
            return servePage(page)
        except amiweb.HTTPError, (e):
            return amiweb.errorHandler(e, environ, start_response)
    raise amiweb.HTTPNotFound()
