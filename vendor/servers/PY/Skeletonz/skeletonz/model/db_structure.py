from amilib import debug_info

from amilib.amiweb import amiweb
from amilib.amiweb.amiweb import db
from amilib.amiweb.amigration import *

from skeletonz.server import getConfig, getPlugins, skeletonz_configurator
import CMSModel as Model

from skeletonz.plugins.upload.model import Files

class Skeletonz_Initial(AmiGration):
    def __init__(self):
        self.table_prefix = getConfig().TABLE_PREFIX

    def up(self):
        self.createTable("file",
          IntCol("id", length=11, auto_increment=True),
          StringCol("ident", length=150, not_null=True),
          StringCol("filename", length=255),
          StringCol("type", length=50, not_null=True),
          PrimaryKey("id"),
          UniqueKey("filename", "filename"),
          utf8=True,
          ignore_if_created=True
        )

        self.createTable("group",
          IntCol("id", length=11, auto_increment=True),
          StringCol("name", length=100, not_null=True),
          PrimaryKey("id"),
          UniqueKey("name", "name"),
          utf8=True,
          ignore_if_created=True
        )

        self.createTable("user",
          IntCol("id", length=11, auto_increment=True),
          StringCol("username", length=100, not_null=True),
          StringCol("password", length=100),
          IntCol("group_id", length=11),
          StringCol("user_type", length=30, default="user"),
          PrimaryKey("id"),
          UniqueKey("username", "username"),
          IndexKey("group_id", "group_id"),
          utf8=True,
          ignore_if_created=True
        )

        self.createTable("menu",
          IntCol("id", length=11, auto_increment=True),
          StringCol("name", length=100, not_null=True),
          IntCol("primary_menu", length=4),
          PrimaryKey("id"),
          UniqueKey("name", "name"),
          utf8=True,
          ignore_if_created=True
        )

        self.createTable("menu_item",
          IntCol("id", length=11, auto_increment=True),
          StringCol("url", length=100),
          StringCol("name", length=100, not_null=True),
          IntCol("m_order", length=4, not_null=True),
          IntCol("menu_id", length=4, not_null=True),
          IntCol("page_id", length=4),
          StringCol("type", length=50, default="page"),
          PrimaryKey("id"),
          IndexKey("menu_id", "menu_id"),
          IndexKey("page_id", "page_id"),
          utf8=True,
          ignore_if_created=True
        )

        self.createTable("page",
          IntCol("id", length=11, auto_increment=True),
          StringCol("name", length=255, not_null=True),
          StringCol("title", length=255, not_null=True),
          StringCol("content"),
          StringCol("premission_type", length=50, default="Every user"),
          StringCol("premission_value", length=50),
          IntCol("hidden", length=4, not_null=True),
          IntCol("menu_id", length=11, not_null=True),
          IntCol("parent_id", length=11),
          PrimaryKey("id"),
          IndexKey("menu_id", "menu_id"),
          IndexKey("parent_id", "parent_id"),
          utf8=True,
          ignore_if_created=True
        )

        self.createTable("page_link",
          IntCol("id", length=11, auto_increment=True),
          StringCol("ident", length=255, not_null=True),
          IntCol("page_id", length=11, not_null=True),
          PrimaryKey("id"),
          UniqueKey("ident", "ident"),
          IndexKey("page_id", "page_id"),
          utf8=True,
          ignore_if_created=True
        )

    def down(self):
        self.dropTable("file", "group", "user",\
            "menu", "menu_item", "page", "page_link")

class Skeletonz_AlterPremissions(AmiGration):
    def __init__(self):
        self.table_prefix = getConfig().TABLE_PREFIX

    def _updatePermission(self, t, set_to):
        pages = amiweb.db().select("page")
        for p in pages:
            if p.premission_type == t:
                amiweb.db().update("page", id=p.id, premission_type=set_to)

    def up(self):
        self.changeColumn("page", StringCol("premission_type", length=50, default="admin"))
        self._updatePermission("Every user", "admin")

    def down(self):
        self.changeColumn("page", StringCol("premission_type", length=50, default="Every user"))
        self._updatePermission("admin", "Every user")

class Skeletonz_AddTypeToMenuItem(AmiGration):
    """
    Fixing a bug...
    """
    def __init__(self):
        self.table_prefix = getConfig().TABLE_PREFIX

    def up(self):
        m_items = amiweb.db().select("menu_item")
        for m in m_items:
            if m.type != "external":
                amiweb.db().update("menu_item", id=m.id, type="page")

    def down(self):
        pass

class Skeletonz_AddPageLog(AmiGration):
    def __init__(self):
        self.table_prefix = getConfig().TABLE_PREFIX

    def up(self):
        self.createTable("pagelog",
          IntCol("id", length=11, auto_increment=True),
          StringCol("edited_by", length=255, not_null=True),
          StringCol("content", length=255, not_null=True),
          IntCol("revision", length=4, not_null=True),
          IntCol("page_id", length=4, not_null=True),
          DateTimeCol("time_of_edit"),
          PrimaryKey("id"),
          utf8=True,
          ignore_if_created=True
        )

    def down(self):
        self.dropTable('pagelog')

class Skeletonz_AlterPageLog(AmiGration):
    def __init__(self):
        self.table_prefix = getConfig().TABLE_PREFIX

    def up(self):
        self.removeColumn("pagelog", 'content')
        self.addColumn('pagelog', StringCol("text", not_null=True))

    def down(self):
        pass

run = False
def fixParentId():
    #Update parent_id on all our current files
    for page in Model.Pages.getAllPages():
        files = Files.getAllFilesByTextScan(page.content)
        for file in files:
            if file.parent_id == None:
                db().update('file', id=file.id, parent_id=page.id)

    #Blog
    for post in db().select('plugin_blog_post'):
        files = Files.getAllFilesByTextScan(post.content)
        parent_id = db().select('plugin_blog', id=post.blog_id, as_one=True).host_page
        for file in files:
            if file.parent_id == None:
                db().update('file', id=file.id, parent_id=parent_id)
    global run
    run = True


class Skeletonz_AddFileParent(AmiGration):

    def __init__(self):
        self.table_prefix = getConfig().TABLE_PREFIX

    def up(self):
        self.addColumn('file', IntCol('parent_id', length=4, default=None))
        fixParentId()

    def down(self):
        self.removeColumn('file', 'parent_id')

class Skeletonz_MadeAnMistake:

    #Must be rerun, because of a bug
    def up(self):
        if not run:
            fixParentId()



mi_control = AMigrationControl("Skeletonz",\
    [Skeletonz_Initial, Skeletonz_AlterPremissions, Skeletonz_AddTypeToMenuItem, Skeletonz_AddPageLog, Skeletonz_AlterPageLog, Skeletonz_AddFileParent, Skeletonz_MadeAnMistake], skeletonz_configurator)

def freshLoadConfig():
    """
    Load the config files over again. Used by backup
    """
    mi_control.invalidate()
    for plugin in getPlugins():
        try:
            plugin['module'].mi_control.invalidate()
        except AttributeError:
            pass


def initDatabaseIfNeeded():
    try:
        mi_control.upgradeToLatest()
    except:
        debug_info.print_info()
        raise

    if Model.Menus.getMenuByName("Standard") == None:
        menu_obj = Model.Menus.add("Standard", primary=True)
        item = Model.MenuItems.add(getConfig().START_PAGE, 1, menu_obj.id)
        amiweb.db().update("page", id=item.getPage().id, hidden=0)

def dropStructure():
    mi_control.downgradeTo(Not_Created)

    #Let plugins add their own table creation
    for plugin in getPlugins():
        try:
            plugin['module'].dropStructure()
        except NotImplementedError:
            pass
