import datetime
import os
import re

from amilib import json
from amilib.amiweb import amiweb
from amilib.amiweb.amigration import *
from amilib.amiweb.amiweb import db
from amilib import PyRSS2Gen

from skeletonz.mylib import converters

from skeletonz.server import getConfig, getRSSManager, getFormatManager
from skeletonz.model import CMSModel

def getDate(date):
    if not date:
        return ''
    now = datetime.datetime.now()
    if now.year == date.year:
        r = date.strftime("%d. %b")
    else:
        r = date.strftime("%d. %b %Y")
    return re.sub('^0', '', r)


class PostDeco:

    def getIdent(self):
        return self.getBlog().ident

    def getBlog(self):
        return BlogModel.getBlogById(self.blog_id)

    def getDate(self):
        return getDate(self.posted)

    def linkAbleTitle(self):
        return converters.makeLinkAble(self.title)

    def hasLabel(self, label_id):
        if self.labels:
            m = re.match('^%s,|.*,%s,' % (label_id, label_id), self.labels)
            if m:
                return True
        return False

    def getLabels(self):
        return BlogLabelModel.getLabelsFromList(self.labels)

    def getHostPage(self):
        blog = BlogModel.getBlogById(self.blog_id)
        return blog.host_page

    def isPublished(self):
        return self.published == 1


class BlogDeco:

    def getLabels(self):
        return BlogLabelModel.getLabelsFromBlog(self.id)

    def getArchiveYears(self):
        qs = db().select('plugin_blog_post', published='1', cols='DISTINCT YEAR(posted) AS year', order_by='posted')
        return [q.year for q in qs]

    def getPostsByYear(self, year):
        return db().select('plugin_blog_post', blog_id=self.id, where='YEAR(posted) = %i' % year, obj_deco=PostDeco, order_by='posted', reversed=True)



class BlogModel:

    def getBlogByIdent(self, ident):
        return db().select('plugin_blog', ident=ident, as_one=True, obj_deco=BlogDeco)

    def getBlogById(self, id):
        return db().select('plugin_blog', id=id, as_one=True, obj_deco=BlogDeco)

    def getAllPosts(self, ident, limit):
        blog = self.getBlogByIdent(ident)
        if blog:
            return db().select("plugin_blog_post", blog_id=blog.id, order_by='posted', reversed=True, limit=limit, obj_deco=PostDeco)
        else:
            return []

    def getPostById(self, id):
        return db().select('plugin_blog_post', id=id, obj_deco=PostDeco, as_one=True)

    def addBlog(self, ident):
        host_page = amiweb.session()['current_page_id']
        db().insert('plugin_blog', ident=ident, host_page=host_page)
        return self.getBlogByIdent(ident)

    def add(self, ident, title, content, labels, posted=None):
        user = amiweb.session().get('username', 'none')

        if not posted:
            posted = datetime.datetime.now()

        blog = self.getBlogByIdent(ident)
        if not blog:
            blog = self.addBlog(ident)

        db().insert('plugin_blog_post', blog_id=blog.id, title=title, content=content, author=user, posted=str(posted), labels=labels)

        updateBlogXML(ident)

    def update(self, id, title, content, labels):
        ident = self.getPostById(id).getIdent()
        db().update('plugin_blog_post', id=id, title=title, content=content, labels=labels)
        updateBlogXML(ident)

    def delete(self, id):
        ident = self.getPostById(id).getIdent()
        db().delete('plugin_blog_post', id=id)
        updateBlogXML(ident)

    def setPublish(self, id, new_val):
        db().update('plugin_blog_post', id=id, published=new_val)

    def flipPublish(self, id):
        p = self.getPostById(id)
        if p.published:
            new_val = 0
        else:
            new_val = 1

        self.setPublish(id, new_val)
        updateBlogXML(p.getIdent())
        return new_val

BlogModel = BlogModel()


class LabelDeco:

    def toJSON(self):
        d = {'name': self.name,
             'content': self.name,
             'id': self.id}
        return json.write(d)

    def getName(self):
        return json.write(self.name)

    def getPosts(self):
        like = 'labels LIKE "%s"'
        like_s = like % (str(self.id) + ",%")
        like_in = like % ("%," + str(self.id) + ",%")
        where = ['%s OR %s' % (like_s, like_in)]
        posts = db().select('plugin_blog_post',
            where="".join(where),
            order_by='posted',
            reversed=True,
            obj_deco=PostDeco)
        return posts

class BlogLabelModel:

    def getById(self, id):
        return db().select('plugin_blog_label', id=id, obj_deco=LabelDeco, as_one=True)

    def getAllByBlogId(self, blog_id):
        return db().select('plugin_blog_label', blog_id=blog_id, order_by='name', obj_deco=LabelDeco)

    def getAllByIdent(self, ident):
        blog = BlogModel.getBlogByIdent(ident)
        if blog:
            return self.getAllByBlogId(blog.id)
        else:
            return []

    def add(self, blog_id, name):
        id = db().insert('plugin_blog_label', name=name, blog_id=blog_id)
        return self.getById(id)

    def delete(self, id):
        db().delete('plugin_blog_label', id=id)

    def update(self, id, name):
        db().update('plugin_blog_label', id=id, name=name)
        return self.getById(id)

    def getLabelsFromList(self, list):
        if list:
            ids = ['id=%s' % i for i in list.split(",")[:-1]]
            labels = db().select('plugin_blog_label', " or ".join(ids), order_by='name', obj_deco=LabelDeco)
            return labels
        else:
            return []

    def getLabelsFromBlog(self, blog_id):
        return db().select('plugin_blog_label', blog_id=blog_id, order_by='name', obj_deco=LabelDeco)

BlogLabelModel = BlogLabelModel()


class CommentDeco:

    def getDate(self):
        return getDate(self.posted)

    def getPost(self):
        return BlogModel.getPostById(self.post_id)

    def toJSON(self):
        d = {'name': self.author,
              'content': '%s: %s...' % (self.author, self.content[0:100]),
             'id': self.id}
        return json.write(d)

class BlogCommentModel:

    def add(self, author, email, website, content, post_id):
        posted = "%s" % datetime.datetime.now()
        return db().insert('plugin_blog_comment', author=author, email=email, website=website, content=content, posted=posted, post_id=post_id)

    def getAll(self, post_id):
        return db().select('plugin_blog_comment', post_id=post_id, order_by='posted', obj_deco=CommentDeco)

    def getAllByIdent(self, ident, after_id=None):
        prefix = db().getPrefix()
        d = {'bp': "%splugin_blog_post" % prefix,
             'bc': "%splugin_blog_comment" % prefix,
             'blog_id': BlogModel.getBlogByIdent(ident).id}

        where = "%(bp)s.blog_id = %(blog_id)s\
            AND %(bp)s.id = %(bc)s.post_id" % d

        if after_id:
            where = '%s AND %s.%s' % (where, d['bc'], 'id < %s' % after_id)

        print where

        order_by = "%(bc)s.posted" % d
        cols = "%(bc)s.id, %(bc)s.content, %(bc)s.author" % d
        return db().select(['plugin_blog_post', 'plugin_blog_comment'], cols=cols, where=where, order_by=order_by, obj_deco=CommentDeco, limit=15, reversed=True)

    def getById(self, id):
        return db().select('plugin_blog_comment', id=id, obj_deco=CommentDeco, as_one=True)

    def delete(self, id):
        db().delete('plugin_blog_comment', id=id)

    def update(self, id, new_val):
        db().update('plugin_blog_comment', id=id, content=new_val)

BlogCommentModel = BlogCommentModel()



class Blog_Initial(AmiGration):

    def __init__(self):
        self.table_prefix = getConfig().TABLE_PREFIX

    def up(self):
        self.createTable("plugin_blog",
          IntCol("id", length=11, auto_increment=True),
          StringCol("ident", length=150, not_null=True),
          StringCol("title", length=255, not_null=True),
          StringCol("content", not_null=True),
          StringCol("author", length=150, not_null=True),
          DateTimeCol("posted"),
          StringCol("labels", length=150),
          IntCol("published", length=2, default=0),
          IntCol("host_page", length=11),
          PrimaryKey("id"),
          utf8=True,
          ignore_if_created=True
        )

        self.createTable("plugin_blog_label",
          IntCol("id", length=11, auto_increment=True),
          StringCol("ident", length=150, not_null=True),
          StringCol("name", length=150, not_null=True),
          PrimaryKey("id"),
          UniqueKey("name", "name"),
          utf8=True,
          ignore_if_created=True
        )

    def down(self):
        self.dropTable('plugin_blog', 'plugin_blog_label')


class Blog_MoveBlogOut(AmiGration):

    def __init__(self):
        self.table_prefix = getConfig().TABLE_PREFIX

    def up(self):
        self.renameTable('plugin_blog', 'plugin_blog_post')

        self.createTable("plugin_blog",
          IntCol("id", length=11, auto_increment=True),
          StringCol("ident", length=150, not_null=True),
          PrimaryKey("id"),
          utf8=True,
          ignore_if_created=True
        )

        self.addColumn('plugin_blog_post', IntCol('blog_id', length=11))

        #Rewrite all the current posts
        posts = db().select('plugin_blog_post')
        for post in posts:
            blog = BlogModel.getBlogByIdent(post.ident)
            db().update('plugin_blog_post', id=post.id, blog_id=blog.id)

        self.removeColumn('plugin_blog_post', 'ident')

    def down(self):
        self.dropTable('plugin_blog')


class Blog_AddComments(AmiGration):

    def __init__(self):
        self.table_prefix = getConfig().TABLE_PREFIX

    def up(self):
        self.createTable("plugin_blog_comment",
          IntCol("id", length=11, auto_increment=True),
          IntCol("post_id", length=11, not_null=True),
          StringCol("author", length=255, not_null=True),
          StringCol("email", length=255),
          StringCol("website"),
          StringCol("content"),
          DateTimeCol('posted'),
          PrimaryKey("id"),
          utf8=True,
          ignore_if_created=True
        )

    def down(self):
        self.dropTable('plugin_blog_comment')

class Blog_RewriteLabels(AmiGration):

    def __init__(self):
        self.table_prefix = getConfig().TABLE_PREFIX

    def up(self):
        self.addColumn('plugin_blog_label', IntCol('blog_id', length=11, not_null=True))
        labels = db().select('plugin_blog_label')
        for label in labels:
            blog = BlogModel.getBlogByIdent(label.ident)
            db().update('plugin_blog_label', id=label.id, blog_id=blog.id)
        self.removeColumn('plugin_blog_label', 'ident')

    def down(self): pass


class Blog_MoveHostPage(AmiGration):

    def __init__(self):
        self.table_prefix = getConfig().TABLE_PREFIX

    def up(self):
        self.addColumn('plugin_blog', IntCol('host_page', length=11, not_null=True))
        blogs = db().select('plugin_blog')

        for b in blogs:
            posts = db().select('plugin_blog_post', blog_id=b.id)
            if len(posts) > 0:
                host_page = posts[0].host_page
                db().update('plugin_blog', id=b.id, host_page=host_page)

        self.removeColumn('plugin_blog_post', 'host_page')

    def down(self): pass


def updateBlogXML(ident):
    from plugin import GENERIC_POST_LINK
    rss_file = "blog_%s.xml" % converters.makeLinkAble(ident)
    rss_items = []

    page_obj = CMSModel.Pages.getPageById(amiweb.session().get('current_page_id', 1))

    def filterContent(text):
        return getFormatManager().htmlFormat(text, False, True, page_obj)

    for item in BlogModel.getAllPosts(ident, 10):
        if item.isPublished():
            item_content = filterContent(item.content)
            link = "%s/%s" % (getConfig().BASE_URL, GENERIC_POST_LINK % item.id)
            rss_items.append(
              PyRSS2Gen.RSSItem(
                title = item.title,
                link = link,
                guid = link,
                description = "%s" % item_content,
                pubDate=item.posted
              )
            )

    host_page = CMSModel.Pages.getPageById(BlogModel.getBlogByIdent(ident).host_page)
    link = "%s/%s" % (getConfig().BASE_URL, host_page.getFullLink())

    rss_obj = PyRSS2Gen.RSS2(
      title = ident,
      link = link,
      description = "",
      lastBuildDate = datetime.datetime.now(),
      items = rss_items
    )

    getRSSManager().publish(rss_file, rss_obj)
