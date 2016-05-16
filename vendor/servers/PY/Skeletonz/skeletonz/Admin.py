import os

from amilib import json
from amilib.amiweb import amiweb, amidb

from mylib.converters import makeLinkAble
from mylib.amicache import AmiCache
from amilib.template import render
from mylib import url_mapper

import model.visit as visit
import model.backup as backup
import model.CMSModel as Model
import model.UserModel as UserModel

from plugins.upload import model as upload_model

import Site

from modules.template import AdminTemplate, SiteEditTemplate, initTemplate

from server import getConfig, getPluginManager, getMainPageId
import server

def checkLoginState():
    Site.Users.checkAdminPermission()

class AdminPage:
    def filter(self):
        checkLoginState()


class Main(AdminPage):

    def __init__(self, template):
        self.template = template

    @amiweb.expose
    def index(self):
        ns = {'template': self.template}
        return render("skeletonz/view/admin/index.tmpl", ns)


class UserManager(AdminPage):

    def __init__(self, template):
        self.template = template

    @amiweb.expose
    def index(self):
        users = UserModel.Users.getAllUsers()
        groups = UserModel.Groups.getAllGroups()

        external_login = True
        try:
            getConfig().PASSWORD_LOOKUP
        except AttributeError:
            external_login = False

        ns = {'users': users,
            'groups': groups,
            'template': self.template,
            'external_login': external_login}
        return render("skeletonz/view/admin/UserManager_index.tmpl", ns)

    @amiweb.expose
    def delete(self, id):
        user = UserModel.Users.getUserById(id)

        #Reset all pages that have this user as their editor
        Model.Pages.resetUserPagePremissions(user.username)

        UserModel.Users.delete(id)
        return "ok"

    @amiweb.expose
    def add(self, username, password, type):
        try:
            item = UserModel.Users.add(username, password, type)
            return item.toJSON()
        except amidb.IntegrityError:
            raise amiweb.AppError("The item was already added.")

    @amiweb.expose
    def changeUserType(self, user_id, type):
        UserModel.Users.changeUserType(user_id, type)
        return "ok"


class GroupManager(AdminPage):

    def filter(self):
        checkLoginState()

    def __init__(self, template):
        self.template = template

    @amiweb.expose
    def index(self):
        groups = UserModel.Groups.getAllGroups()
        ns = {'groups': groups,
            'template': self.template}
        return render("skeletonz/view/admin/GroupManager_index.tmpl", ns)

    @amiweb.expose
    def delete(self, id):
        group = UserModel.Groups.getGroupById(id)
        #Reset all pages that have this group as their editor
        Model.Pages.resetGroupPagePremissions(group.name)

        UserModel.Groups.delete(id)
        return "ok"

    @amiweb.expose
    def add(self, name):
        try:
            item = UserModel.Groups.add(name)
            return item.toJSON()
        except amidb.IntegrityError:
            raise amiweb.AppError("The item was already added.")

    @amiweb.expose
    def changeUserGroup(self, group_id, user_id):
        UserModel.Groups.changeUserGroup(group_id, user_id)
        return "ok"


class UploadManager(AdminPage):

    def filter(self):
        checkLoginState()

    def __init__(self, template=None):
        self.template = template

    def expirePages(self, id):
        for page in upload_model.Files.getAllPagesWhereFileIsUsed(id):
            AmiCache.expireCache(page.id)

    @amiweb.expose
    def index(self):
        pages = Model.Pages.getAllPages()
        ns = {'pages': pages,
              'template': self.template}
        functions = 'skeletonz/view/admin/components/page_files.tmpl'
        return render("skeletonz/view/admin/UploadManager_index.tmpl", ns, base=functions)

    @amiweb.expose
    def delete(self, id):
        self.expirePages(id)
        upload_model.Files.deleteFileById(id)
        return "ok"


class SiteManager(AdminPage):

    def __init__(self, template):
        self.template = template

    @amiweb.expose
    def index(self):
        sitemap_repr = visit.getSitemap()

        pages = Model.Pages.getAllPages()
        ns = {'pages': pages,
              'template': self.template,
              'sitemap_repr': sitemap_repr,
              'getPermissionDropBox': PermissionManager.getPermissionDropBox,
              'main_page_id': getMainPageId()}
        return render("skeletonz/view/admin/SiteManager_index.tmpl", ns)

    @amiweb.expose
    def changePremission(self, page_id, type, value):
        Model.Pages.changePagePremissions(page_id, type, value)
        return "ok"

    @amiweb.expose
    def deletePage(self, id):
        Model.Pages.deletePageById(id)
        AmiCache.expireAllPages()
        return "ok"


class BackupManager(AdminPage):

    def __init__(self, template):
        self.template = template

    @amiweb.expose
    def index(self):
        all_snapshots = backup.SnapShots.getAllSnapShots()
        ns = {'template': self.template,
              'all_snapshots': all_snapshots}
        return render("skeletonz/view/admin/BackupManager_index.tmpl", ns)

    @amiweb.expose
    def createSnapshot(self, name, order):
        m_tables = amiweb.db().query("SHOW TABLES;")
        order = int(order)

        s = backup.SnapShot()
        s.createZip(order, name)
        json_repr = '{"order": %s, "content": "%s - %s", "filename": "%s.zip", "insert_in_top": 1}' %\
                    (order, name, backup.TODAY, s._generateSnapshotFilename(order, name))
        return json_repr

    @amiweb.expose
    def restoreSnapshot(self, filename):
        s = backup.SnapShot()
        s.restoreZip(filename)
        AmiCache.expireAllPages()
        return "ok"

    @amiweb.expose
    def deleteSnapshot(self, filename):
        os.remove("%s/%s" % (backup.backup_dir, filename))
        return "ok"

    @amiweb.expose
    @amiweb.staticHandler
    def downloadSnapshot(self, filename):
        full_path = '%s/dynamic_dirs/backup/%s' % (os.getcwd(), filename)
        return full_path



class ServerManager(AdminPage):

    def __init__(self, template):
        self.template = template

    @amiweb.expose
    def index(self):
        ns = {'template': self.template,
              'cache_up_to_date': AmiCache.areAllPagesUp2Date()}
        return render("skeletonz/view/admin/ServerManager_index.tmpl", ns)

    @amiweb.expose
    def restart_cache(self):
        url_mapper.expirePageMemo()
        AmiCache.expireAllPages()
        AmiCache.cacheAllPages()
        return "ok"

    @amiweb.expose
    def stop_server(self):
        import os
        pid = os.getpid()
        os.kill(pid, 1)


class PluginManager(AdminPage):

    def __init__(self, template):
        self.template = template

    @amiweb.expose
    def index(self):
        ns = {'template': self.template,
              'plugins': getPluginManager().listAvailablePlugins()}
        return render("skeletonz/view/admin/PluginManager_index.tmpl", ns)

    @amiweb.expose
    def reloadPlugins(self):
        getPluginManager().clearPlugins()
        getPluginManager().importPlugins()

    @amiweb.expose
    def adminInterface(self,plugin):
        ns = {'template': self.template,
              'plugin': plugin,
              'options': getPluginManager().getPluginOptions(plugin)}
        return render("skeletonz/view/admin/PluginManager_adminInterface.tmpl", ns)

    @amiweb.expose
    def savePluginOptions(self,plugin,values):
        values = json.read(values)
        options = getPluginManager().getPluginOptions(plugin)
        for index,option in enumerate(options):
            option.value = values[index]
            option.save()
        return "ok"


class TemplateManager(AdminPage):

    def __init__(self, template):
        self.template = template

    @amiweb.expose
    def index(self):
        available_templates = self.getAvailableTemplates()
        current_template = self.getCurrentTemplate()
        ns = {'template': self.template,
              'available_templates':available_templates,
              'current_template':current_template}
        return render("skeletonz/view/admin/TemplateManager_index.tmpl", ns)

    @amiweb.expose
    def changeTemplate(self, id):
        try:
            exec "from templates.%s import template as m" % json.read(id)
            old_template_name = getConfig().CURRENT_TEMPLATE.NAME
            getConfig().CURRENT_TEMPLATE = m

            AmiCache.expireAllPages()

            rc = server.getRootController()

            site_template = m.SiteTemplate()
            site_edit_template = SiteEditTemplate(m.SiteTemplate)

            site_template.markChildren()

            rc.root_obj.template = site_template
            rc.root_obj.siteedit.template = site_edit_template
            initTemplate(site_template, 'addToSiteTemplate')
            initTemplate(site_edit_template, 'addToSiteEditTemplate')

            server.setTemplateStaticPaths(rc, old_template_name)

        except Exception, inst:
            print "'%s' is not a valid template" % inst

    def getAvailableTemplates(self):
        template_dir = './templates'
        templates = []
        for pa in os.listdir(template_dir):
            if os.path.isdir(os.path.join(template_dir, pa)) and pa[0] != ".":
                templates.append(pa)
        return [json.write({'id': t, 'content': t}) for t in templates]

    def getCurrentTemplate(self):
        return getConfig().CURRENT_TEMPLATE.NAME


class PermissionManager(AdminPage):

    def getPermissionDropBox(self, page_id, change_url="admin/SiteManager/changePremission?"):
        getPremission = Model.Pages.getPremission
        premissions_repr = visit.getPremissions()

        ns = {'page_id': page_id,
              'premissions_repr': premissions_repr,
              'getPagePremission': getPremission,
              'change_url': change_url}
        return render("skeletonz/view/admin/components/permission_manager.tmpl", ns)

PermissionManager = PermissionManager()
