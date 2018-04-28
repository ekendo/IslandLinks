from skeletonz.server import getConfig, getFormatManager, getCurrentPage
import skeletonz.model.CMSModel as Model
from skeletonz.modules import sections
from amilib.amiweb.amiweb import db, session, setUpConnection

class AmiCacher:

    def __init__(self):
        self.pages_cached = {}

    def expireAllPages(self):
        self.pages_cached = {}

    def isCacheUp2Date(self, page_id):
        page_id = str(page_id)
        if not getConfig().USE_CACHING:
            return False
        #(should_do_update:bool, value)
        if self.pages_cached.get(page_id, False):
            #Check to see if we should get a new update
            if self.pages_cached[page_id][0] == False:
                return True
            else:
                return False
        else:
            return False

    def updateCache(self, page_id, new_value):
        page_id = str(page_id)
        if not getConfig().USE_CACHING:
            return new_value
        try: del self.pages_cached[page_id]
        except: pass

        self.pages_cached[page_id] = (False, new_value)
        return new_value

    def expireCache(self, page_id):
        page_id = str(page_id)
        if not getConfig().USE_CACHING:
            return False
        self.pages_cached[page_id] = (True, None)

    def expireCurrentPage(self):
        if not getConfig().USE_CACHING:
            return False
        page_id = session()['current_page_id']
        self.expireCache(page_id)

    def getValue(self, page_id):
        page_id = str(page_id)
        if not getConfig().USE_CACHING:
            return False
        return self.pages_cached[page_id][1]

    def cachePageById(self, page_id):
        page = Model.Pages.getPageById(page_id)
        self.cachePage(page)

    def cachePage(self, page):
        print "[amicache] Amicacher cachePage: hit the cache logic<br>"
        from skeletonz.Site import Site, renderView, getCurrentInfo

        tmpl = getConfig().CURRENT_TEMPLATE.SiteTemplate()
        tmpl.markChildren()

        site = Site(tmpl)

        getFormatManager().resetPagePlugins()

        current_info = getCurrentInfo(site.template, page.id, False)
        current_info['is_admin'] = False
        current_info['logged_in'] = False
        current_info['edit_mode'] = False
        current_info['edit_permission'] = None

        content = renderView(current_info, "site_structure")

        #Append sections to this
        content = sections.fillSections(current_info, content, page)

        self.updateCache(page.id, content)


    def cacheAllPages(self):
        if not getConfig().USE_CACHING:
            return False

        #Maybe function called from a new thread
        try:
            gb = db()
        except AttributeError:
            setUpConnection()

        self.expireAllPages()

        pages = Model.Pages.getAllPages()

        cur_page = getCurrentPage()

        for page in pages:
            session()['current_page_id'] = page.id
            self.cachePage(page)

        session()['current_page_id'] = cur_page
        return "ok"

    def areAllPagesUp2Date(self):
        if len(self.pages_cached) == 0:
            return False
        for k in self.pages_cached.keys():
            if self.pages_cached[k][0]:
                return False
        return True


class AmiCacheAdapter:
    """
    I first made this work only for nrm_pages,
    then I figured out it was probably a good idea to support edit pages aswell
    """
    def __init__(self):
        self.nrm_pages = AmiCacher()
        self.edit_pages = AmiCacher()

    def expireEditPages(self):
        self.edit_pages.expireAllPages()

    def expireAllPages(self):
        self.edit_pages.expireAllPages()
        self.nrm_pages.expireAllPages()

    def isCacheUp2Date(self, page_id, is_edit=False):
        if is_edit: return self.edit_pages.isCacheUp2Date(page_id)
        else: return self.nrm_pages.isCacheUp2Date(page_id)

    def updateCache(self, page_id, new_value, is_edit=False):
        if is_edit: return self.edit_pages.updateCache(page_id, new_value)
        else: return self.nrm_pages.updateCache(page_id, new_value)

    def expireCache(self, page_id):
        self.edit_pages.expireCache(page_id)
        return self.nrm_pages.expireCache(page_id)

    def expireCurrentPage(self):
        self.edit_pages.expireCurrentPage()
        return self.nrm_pages.expireCurrentPage()

    def cachePageById(self, id):
        return self.nrm_pages.cachePageById(id)

    def cacheAllPages(self):
        return self.nrm_pages.cacheAllPages()

    def getValue(self, page_id, is_edit=False):
        if is_edit: return self.edit_pages.getValue(page_id)
        else: return self.nrm_pages.getValue(page_id)

    def areAllPagesUp2Date(self, is_edit=False):
        if is_edit: return self.edit_pages.areAllPagesUp2Date()
        else: return self.nrm_pages.areAllPagesUp2Date()

AmiCache = AmiCacheAdapter()
