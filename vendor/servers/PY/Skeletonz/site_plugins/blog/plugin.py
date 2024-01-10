import os
import urllib
import re
import random
#import md5
import hashlib

from amilib.useful import Singleton
from amilib.amiweb.amiweb import session
from amilib.amiweb.amidb import IntegrityError
from amilib.template import render
from amilib.amiweb.amigration import AMigrationControl, Not_Created
from amilib import json

from skeletonz.modules.plugin import GenericPlugin
from skeletonz.mylib import html_helpers, converters
from skeletonz.server import getConfig, getRSSManager, plugin_configurator, getRootController, getFormatManager, getMailManager

from skeletonz.model import CMSModel

from model import BlogModel, BlogLabelModel, BlogCommentModel
import model

from skeletonz.Site import adminPermission, editPermission


PLUGINS_FOR_EXPORT = ['Blog']

#--- Constants ----------------------------------------------
GENERIC_POST_LINK = 'blog/viewEntry/%s'
GENERIC_LABEL_LINK = 'blog/viewLabelPosts/%s'
GENERIC_ARCHIVE_LINK = 'blog/viewArchive/%s-%s'
GENERIC_DELETE_LINK = 'blog/deleteComment?id=%s'


class Blog(GenericPlugin):
    NAME = "Blog plugin"
    DESCRIPTION = "Adds a blog...!"
    SYNTAX = [
        {'handler': 'blog',
         'required_arguments': {'ident': 'The identification'},
         'optional_arguments': {'with_blog_info': {'type': 'option', 'help': 'Shows labels and archive'}}
        },
        {'handler': 'blog_labels',
         'required_arguments': {'ident': 'The identification'},
         'optional_arguments': {}
        },
        {'handler': 'blog_archive',
         'required_arguments': {'ident': 'The identification'},
         'optional_arguments': {}
        },
        {'handler': 'blog_rsslink',
         'required_arguments': {'ident': 'The identification'},
         'optional_arguments': {}
        },
      ]

    def __init__(self):
        self.mi_control = AMigrationControl("Blog",
            [model.Blog_Initial, model.Blog_MoveBlogOut,
             model.Blog_AddComments, model.Blog_RewriteLabels,
             model.Blog_MoveHostPage], plugin_configurator)

        format_man = getFormatManager()
        format_man.registerSLPlugin('blog', self.handleBlog)
        format_man.registerSLPlugin('blog_labels', self.handleBlogLabels)
        format_man.registerSLPlugin('blog_archive', self.handleBlogArchive)
        format_man.registerSLPlugin('blog_rsslink', self.handleBlogRssLink)

    def createStructure(self):
        self.mi_control.upgradeToLatest()

    def dropStructure(self):
        self.mi_control.downgradeTo(Not_Created)

    def addToController(self, rc):
        path = "%s/site_plugins/blog/static" % os.getcwd()
        rc.addStaticPath("/static_plugin/blog/", path)

        rc.root_obj.blog = BlogController
        rc.root_obj.plugin.blog = BlogController

    def _addTemplate(self, template):
        s = open("site_plugins/blog/static/style.css").read()
        #template.getHeader().appendStyleData('<style type="text/css">%s</style>' % s)

        s = open("site_plugins/blog/static/script_public.js").read()
        #template.getHeader().appendScriptData('<script type="text/javascript">%s</script>' % s)

    def addToSiteTemplate(self, template, on_init):
        self._addTemplate(template)

    def addToSiteEditTemplate(self, template, on_init):
        s = open("site_plugins/blog/static/script.js").read()
        template.getHeader().appendStaticScriptData(s)

        self._addTemplate(template)

#--- Handlers ----------------------------------------------
    def handleBlog(self, args, edit_mode, page_id):
        ident = args.get('blog')

        if ident:
            result = []
            if edit_mode:
                result.append(self.renderManage(ident))
            if args.get('with_blog_info', None) != None:
                result.append(self.renderBlogInfo(ident, edit_mode))
            result.append(self.renderContent(ident, edit_mode))
            return True, "".join(result)

    def handleBlogLabels(self, args, edit_mode, page_id):
        ident = args.get('blog_labels')
        if ident:
            return True, BlogInfo().getLabelList(ident, '')

    def handleBlogArchive(self, args, edit_mode, page_id):
        ident = args.get('blog_archive')
        if ident:
            return True, BlogInfo().getArchiveList(ident, '')

    def handleBlogRssLink(self, args, edit_mode, page_id):
        ident = args.get('blog_rsslink')
        if ident:
            return False, BlogInfo().getRSSLink(ident)

#--- Renders ----------------------------------------------
    def renderManage(self, ident):
        btn_add_post = html_helpers.createActionLink("Add post", "static_plugin/blog/add.png", "return Blog.viewAdd('%s', this);" % ident)
        btn_manage_label = html_helpers.createActionLink("Labels", "static_plugin/blog/label_manage.png", "return Blog.viewLabelManager('%s', this);" % ident)
        btn_manage_comments = html_helpers.createActionLink("Comments", "static_plugin/blog/comment_manage.png", "return Blog.viewCommentManager('%s', this);" % ident)

        ns = {
          'btn_add_post': btn_add_post,
          'btn_manage_label': btn_manage_label,
          'btn_manage_comments': btn_manage_comments,
          'ident': ident
        }
        return render("site_plugins/blog/view/manage.tmpl", ns)

    def renderBlogInfo(self, ident, edit_mode):
        return BlogInfo().render(ident, edit_mode)

    def renderContent(self, ident, edit_mode):
        posts = BlogModel.getAllPosts(ident, 15)

        ns = {
          'ident': ident,
          'posts': posts,
          'edit_mode': edit_mode,
          'renderPost': renderPost
        }
        return render("site_plugins/blog/view/items.tmpl", ns)


#--- Render for blog info ----------------------------------------------
class BlogInfo(Singleton):

    def getLabelList(self, ident, cls):
        blog = BlogModel.getBlogByIdent(ident)
        if blog:
            link = '<a href="%s">%s</a>' % (GENERIC_LABEL_LINK, '%s')
            labels = ['<li>%s</li>' % (link % (l.id, l.name)) for l in blog.getLabels()]
            return '<ul class="%s">\n%s\n</ul>' % (cls, '\n'.join(labels))
        else:
            return None

    def getArchiveList(self, ident, cls):
        blog = BlogModel.getBlogByIdent(ident)
        if blog:
            link = '<a href="%s">%s</a>' % (GENERIC_ARCHIVE_LINK % (blog.id, '%s'), '%s')
            years = ['<li>Posts in %s</li>' % (link % (y, y)) for y in blog.getArchiveYears()]
            return '<ul class="%s">\n%s\n</ul>' % (cls, '\n'.join(years))
        else:
            return None

    def getRSSLink(self, ident):
        rss_link = "%s/rss/blog_%s.xml" % (getConfig().BASE_URL, converters.makeLinkAble(ident))
        return """<a href="%s"><img src="static_core/images/rss.gif" alt="RSS blog feed" /></a>""" %\
            (rss_link)

    def render(self, ident, edit_mode):
        labels = self.getLabelList(ident, 'CMS_BlogInfo_Labels')
        archive = self.getArchiveList(ident, 'CMS_BlogInfo_Archive')
        rss_link = self.getRSSLink(ident)
        if labels:
            return """
      <div class="CMS_BlogInfo">
        <div class="head">Blog info</div>

        <div class="caption">Labels:</div>
        <div> %s </div>

        <div class="caption">Archive:</div>
        <div> %s </div>

        <div class="caption"> %s </div>
      </div>
          """ % (labels, archive, rss_link)
        else:
            return ''

#--- Renders for comment and post ----------------------------------------------
def renderPostComment(post_id):
    ns = {'post_id': post_id}
    return render("site_plugins/blog/view/post_comment.tmpl", ns)

def renderComment(comment, is_last, edit_mode):
    def postAuthor(author, website):
        if website:
            return '<a href="%s" target="_blank">%s</a>' % (website, author)
        return author

    if edit_mode:
        btn_edit_comment = html_helpers.createActionLink("Edit comment", "static_core/images/edit.png", "return Blog.editComment('%s', this);", tooltip_inner=True)
        btn_del_comment = html_helpers.createActionLink("Delete comment", "static_core/images/trash.png", "return Blog.deleteComment('%s', this);", tooltip_inner=True, confirm='Are you sure you want to delete?')
    else:
        btn_del_comment = ''
        btn_edit_comment = ''

    def amiformat(cnt, var):
        script = re.compile('<(/?script)>', re.IGNORECASE)
        cnt = script.sub(r'&lt;\1&gt;', cnt)
        return getFormatManager().noPluginFormat(cnt, var)

    ns = {'comment': comment,
          'postAuthor': postAuthor,
          'btn_edit_comment': btn_edit_comment,
          'btn_del_comment': btn_del_comment,
          'amiformat': amiformat,
          'is_last': is_last,
          'edit_mode': edit_mode}
    return render("site_plugins/blog/view/comment.tmpl", ns)

def renderPost(post, edit_mode, is_permanent=False):
    page_obj = CMSModel.Pages.getPageById(post.getHostPage())

    def cmsRender(text):
        return getFormatManager().htmlFormat(text, edit_mode, False, page_obj)

    #Buttons
    if edit_mode:
        btn_del = html_helpers.createActionLink("Delete post", "static_core/images/trash.png", "return Blog.del(this, '%s');" % post.getIdent(), tooltip_inner=True, confirm='Are you sure you want to delete?')
        btn_edit = html_helpers.createActionLink("Edit post", "static_core/images/edit.png", "return Blog.viewEdit('%s', this);" % post.getIdent(), tooltip_inner=True)
        btn_published = html_helpers.createActionLink("Published", "static_core/images/%(image)s", "return Blog.publish(this, '%s');" % post.getIdent(), tooltip_inner=True)

        d = post.published and {'image': 'on.png'} or {'image': 'off.png'}
        btn_published = btn_published % d
    else:
        btn_del = ''
        btn_edit = ''
        btn_published = ''

    ns = {
      'post': post,
      'btn_delete': btn_del,
      'btn_edit': btn_edit,
      'btn_published': btn_published,
      'cmsRender': cmsRender,
      'comments': BlogCommentModel.getAll(post.id),
      'edit_mode': edit_mode,
      'is_permanent': is_permanent,
      'post_comment_div': renderPostComment(post.id),
      'GENERIC_POST_LINK': GENERIC_POST_LINK,
      'GENERIC_LABEL_LINK': GENERIC_LABEL_LINK,
      'renderComment': renderComment
    }

    if post.published or edit_mode:
        #Check and see if the template has a inject method
        site_obj = getRootController().root_obj
        if getattr(site_obj.template, 'plugin_blog_renderPost', False):
            return site_obj.template.plugin_blog_renderPost(ns)
        else:
            return render("site_plugins/blog/view/item.tmpl", ns)
    else:
        return 'No post found.'


#--- Controller ----------------------------------------------
from skeletonz import Site

from skeletonz.modules.template import PluginTemplate
from amilib.amiweb import amiweb
from skeletonz.mylib.amicache import AmiCache


class LimitedDictionary(dict):

    size = 50
    key_list = []

    def __setitem__(self, name, value):
        self.key_list.append(name)
        self._clean_up()
        dict.__setitem__(self, name, value)

    def _clean_up(self):
        while len(self.key_list) > self.size:
            name = self.key_list.pop(0)
            del self[name]


class BlogController:

    def __init__(self):
        template = PluginTemplate("blog")
        self.template = template
        self.obj_blog = Blog()

        self.url_cache = LimitedDictionary()

    @amiweb.expose
    def previewComment(self):
        ns = {
            'template': self.template
        }
        return render("site_plugins/blog/view/preview_post.tmpl", ns)

    @amiweb.expose
    def renderComment(self, content):
        return getFormatManager().noPluginFormat(content, True)

    @amiweb.expose
    @editPermission
    def viewAdd(self, ident):
        ns = {'template': self.template,
              'ident': ident,
              'title': '',
              'content': '',
              'hasLabel': lambda id: False,
              'action': 'add',
              'submit_value': 'Add post',
              'labels': BlogLabelModel.getAllByIdent(ident)
              }
        return render("site_plugins/blog/view/manage_post.tmpl", ns)

    @amiweb.expose
    @amiweb.customHandler
    def viewEntry(self, path_info, formvars):
        post_id = path_info.split("/")[-1]
        if amiweb.request()['QUERY_STRING']:
            post_id = amiweb.request()['QUERY_STRING'].split('=')[-1]
        post = BlogModel.getPostById(post_id)
        edit_mode = False
        if Site.Users.isLoggedIn():
            edit_mode = True

        kw = {}
        if post:
            blog = BlogModel.getBlogById(post.blog_id)
            page = CMSModel.Pages.getPageById(blog.host_page)
            content = '<div id="Blog_%s" post_id="%s">%s</div>' %\
                (post.getIdent(), post.id, renderPost(post, edit_mode, True))
            kw['content'] = content
            kw['title'] = post.title
            kw['host_page'] = blog.host_page
            kw['id'] = "blogpost_%s" % post_id
            kw['hidden'] = page.hidden
            kw['premission_type'] = page.premission_type
        else:
            kw['title'] = 'Not found'
            kw['content'] = 'Not found'
            kw['host_page'] = 1
            kw['id'] = None
            kw['hidden'] = False
            kw['premission_type'] = 'Everyone'
        kw['edit_mode'] = edit_mode

        page_obj = Site.PageDeco(kw)
        return page_obj.servePage()

    @amiweb.expose
    @editPermission
    def viewEdit(self, id):
        post = BlogModel.getPostById(id)

        ns = {'template': self.template,
              'ident': id,
              'title': post.title,
              'content': post.content,
              'hasLabel': post.hasLabel,
              'action': 'update',
              'submit_value': 'Save changes',
              'labels': BlogLabelModel.getAllByIdent(post.getIdent())
              }
        return render("site_plugins/blog/view/manage_post.tmpl", ns)

    @amiweb.expose
    @editPermission
    def getHTMLData(self, ident):
        return self.obj_blog.renderContent(ident, True)

    @amiweb.expose
    @editPermission
    def getPostHTMLData(self, id, is_permanent):
        if is_permanent == 'True':
            is_permanent = True
        else:
            is_permanent = False
        post = BlogModel.getPostById(id)
        return renderPost(post, True, is_permanent)

    @amiweb.expose
    @editPermission
    def add(self, ident, title, content, labels):
        AmiCache.expireCurrentPage()
        BlogModel.add(ident, title, content, labels)
        return ident

    @amiweb.expose
    @editPermission
    def update(self, ident, title, content, labels):
        id = ident
        self._expireCache(id)

        BlogModel.update(id, title, content, labels)
        p = BlogModel.getPostById(id)
        return '%s' % p.id

    @amiweb.expose
    @editPermission
    def delete(self, id):
        self._expireCache(id)
        BlogModel.delete(id)
        return 'ok'

    @amiweb.expose
    @editPermission
    def flipPublish(self, id):
        self._expireCache(id)
        return str(BlogModel.flipPublish(id))

    def _expireCache(self, post_id):
        post = BlogModel.getPostById(post_id)
        blog = post.getBlog()

        AmiCache.expireCache(post.getHostPage())
        AmiCache.expireCache('blogpost_%s' % post_id)

        for l in post.getLabels():
            AmiCache.expireCache('bloglabel_%s' % l.id)

        for y in blog.getArchiveYears():
            AmiCache.expireCache('blogarchive_%s-%s' % (blog.id, y))


    ##
    # Labels
    #
    @amiweb.expose
    @amiweb.customHandler
    def viewLabelPosts(self, path_info, formvars):
        label_id = path_info.split('/')[-1]
        kw = {}
        label = BlogLabelModel.getById(label_id)
        posts = label.getPosts()
        blog = BlogModel.getBlogById(label.blog_id)

        title = '%s posts posted under label "%s"' % (len(posts), label.name)
        ns = {
          'posts': posts,
          'amiformat': getFormatManager().noPluginFormat,
          'title': title,
          'GENERIC_POST_LINK': GENERIC_POST_LINK
        }
        kw['content'] = render('site_plugins/blog/view/viewPosts.tmpl', ns)

        kw['title'] = title
        kw['host_page'] = blog.host_page
        kw['id'] = "bloglabel_%s" % label_id
        kw['hidden'] = False
        kw['edit_mode'] = False
        kw['premission_type'] = 'Everyone'
        page_obj = Site.PageDeco(kw)
        return page_obj.servePage()

    @amiweb.expose
    @editPermission
    def viewLabelManager(self, ident):
        ns = {
          'ident': ident,
          'template': self.template,
          'labels': BlogLabelModel.getAllByIdent(ident)
        }
        return render("site_plugins/blog/view/label_manager.tmpl", ns)

    @amiweb.expose
    @editPermission
    def viewCommentManager(self, ident):
        ns = {
          'ident': ident,
          'template': self.template,
          'comments': BlogCommentModel.getAllByIdent(ident)
        }
        return render("site_plugins/blog/view/comment_manager.tmpl", ns)

    @amiweb.expose
    @editPermission
    def labelAdd(self, ident, name):
        blog = BlogModel.getBlogByIdent(ident)
        if not blog:
            blog = BlogModel.addBlog(ident)

        try:
            return BlogLabelModel.add(blog.id, name).toJSON()
        except IntegrityError:
            raise amiweb.AppError('A label is already found with the same name.')

    @amiweb.expose
    @editPermission
    def labelDelete(self, id, ident):
        return BlogLabelModel.delete(id)

    @amiweb.expose
    @editPermission
    def labelUpdate(self, id, name):
        AmiCache.expireCurrentPage()
        try:
            return BlogLabelModel.update(id, name).toJSON()
        except IntegrityError:
            raise amiweb.AppError('A label is already found with the same name.')



#--- Archive ----------------------------------------------
    @amiweb.expose
    @amiweb.customHandler
    def viewArchive(self, path_info, formvars):
        blogid_year = path_info.split('/')[-1]
        sp = blogid_year.split('-')
        blog_id = sp[0]
        year = int(sp[1])

        kw = {}
        blog = BlogModel.getBlogById(blog_id)
        posts = blog.getPostsByYear(year)

        title = 'Post archive for year %s' % year
        ns = {
          'posts': posts,
          'amiformat': getFormatManager().noPluginFormat,
          'title': title,
          'GENERIC_POST_LINK': GENERIC_POST_LINK
        }
        kw['content'] = render('site_plugins/blog/view/viewPosts.tmpl', ns)

        kw['title'] = title
        kw['host_page'] = blog.host_page
        kw['id'] = "blogarchive_%s" % blogid_year
        kw['hidden'] = False
        kw['edit_mode'] = False
        kw['premission_type'] = 'Everyone'
        page_obj = Site.PageDeco(kw)
        return page_obj.servePage()


#--- Comments ----------------------------------------------
    def _expireCommentCache(self, id):
        comment = BlogCommentModel.getById(id)
        self._expireCache(comment.post_id)

    def _getCommentURL(self, post_id, cmnt_id):
        url = "%s/%s#%s" % (getConfig().BASE_URL, GENERIC_POST_LINK % post_id, cmnt_id)
        return url

    def _getDeleteURL(self, cmnt_id):
        url = "%s/%s" % (getConfig().BASE_URL, GENERIC_DELETE_LINK % cmnt_id)
        return url

    @amiweb.expose
    def viewComment(self, id):
        comment = BlogCommentModel.getById(id)
        post = comment.getPost()
        url = self._getCommentURL(post.id, id)
        raise amiweb.HTTPFound(url)


    #--- Captcha ----------------------------------------------
    re_all_img = re.compile('<img.+?>')
    re_img_src = re.compile('src="(.+?)"')
    def _getPictures(self, type, count):
        page = random.randint(0, 35)

        if type == 'birds':
            url = 'http://www.flickr.com/groups/beautifulbirdscom/pool/page%s' % page
        elif type == 'cats':
            url = 'http://www.flickr.com/photos/tags/kitty/clusters/cat-kitten-cute/page%s' % page
        else:
            raise Exception('Invalid type arugment, should be "birds" or "cats", you gave %s' % type)

        result = []
        html = urllib.urlopen(url).read()

        img_tags = list(self.re_all_img.finditer(html))
        random.shuffle(img_tags)

        for img_tag in img_tags:
            img_tag = img_tag.group(0)
            if 'class="pc_img"' in img_tag:
                result.append( self.re_img_src.search(img_tag).group(1) )

            if len(result) >= count:
                break

        return result

    @amiweb.expose
    def getCaptchaHTML(self):
        matches = self._getPictures('birds', 5)

        cats = self._getPictures('cats', 1)
        session()['captcha_current_url'] = cats[0]

        matches.extend(cats)
        random.shuffle(matches)

        form_html = []
        form_html.append('<form><table>')

        li_item = '<td><input type="radio" name="c_match" value="%s" id="%s" />'\
                  '<label for="%s"><img src="%s" /></label></td>'

        for i in range(0, len(matches), 2):
            img_1 = matches[i]
            img_2 = matches[i+1]
            form_html.append('<tr>')
            id = 'item_%s' % i
            id2 = 'item_%s' % (i+1)
            form_html.append(li_item % (img_1, id, id, img_1))
            form_html.append(li_item % (img_2, id2, id2, img_2))
            form_html.append('</tr>')

        form_html.append('</table></form>')
        return ' '.join(form_html)

    @amiweb.expose
    def validateCaptcha(self, url, content):
        cur_url = session().get('captcha_current_url')
        if url == cur_url:
            m = md5.new()
            m.update(content)
            session()['captcha_ok_for'] = m.hexdigest()
            return 'ok'

        #Only one guess
        if cur_url:
            del session()['captcha_current_url']

        return 'error'

    @amiweb.expose
    def showCaptcha(self):
        ns = {
            'template': self.template
        }
        return render("site_plugins/blog/view/show_captcha.tmpl", ns)

    urls = re.compile('https?://[^\s?]+')
    def checkURLSpam(self, author, content):
        for m in self.urls.finditer(content):
            url = m.group(0)
            url_key = 'af_url_%s_%s' % (hash(author), hash(url))
            count = self.url_cache.get(url_key, 0)

            if count >= 2:
                raise SpamComment()

            if url_key not in self.url_cache:
                self.url_cache[url_key] = 0

            self.url_cache[url_key] += 1


    #--- Post and edit ----------------------------------------------
    @amiweb.expose
    def addComment(self, author, email, website, content, post_id):
        #Check captcha
        m = md5.new()
        m.update(content)
        hash_val = m.hexdigest()
        if hash_val != session().get('captcha_ok_for'):
            raise Exception('Wrong captcha check')
        else:
            del session()['captcha_ok_for']

        if website != '' and website.find("http://") != 0:
            website = "http://%s" % (website)

        #Strip scripts, style and meta
        content = re.sub('<(script|meta|style)(.|\s)*?>(.|\s)*?</(script|meta|style)>', '', content)

        #Check for same URL's posted
        try:
            self.checkURLSpam(author, content)
        except SpamComment:
            return '<p><b style="background-color: #ffffcc; color: red">Your comment was marked as spam.</b>, but will be readded if it isn\'t.</p>'

        self._expireCache(post_id)
        id = BlogCommentModel.add(author, email, website, content, post_id)
        email_data = {
            'title': 'An comment has been posted',
            'author': author,
            'email': email,
            'website': website,
            'content': content,
            'delete_link': self._getDeleteURL(id),
            'post_id': self._getCommentURL(post_id, id)
        }

        #Send a notification email
        if hasattr(getConfig(), 'DEFAULT_EMAIL'):
            text = """%(title)s

Author: %(author)s
Email: %(email)s
Website: %(website)s
Post link: %(post_id)s

Content:
%(content)s



Delete link: %(delete_link)s
""" % email_data
            mail = getConfig().DEFAULT_EMAIL
            getMailManager().sendEmail(mail, [mail], '[Skeletonz] %s' % email_data['title'], text)

        if id:
            return renderComment(BlogCommentModel.getById(id), True, False)
        else:
            return '<p><b style="background-color: #ffffcc; color: red">Your comment was marked as spam.</b>, but will be readded if it isn\'t.</p>'


    @amiweb.expose
    @editPermission
    def deleteComment(self, id, ident=""):
        c = BlogCommentModel.getById(id)
        self._expireCommentCache(id)
        #BlogCommentModel.delete(id)
        return c.post_id

    @amiweb.expose
    @editPermission
    def deleteComments(self, ids, ident=""):
        ids = json.read(ids)

        if len(ids) > 0:
            first_id = ids[0]
            c = BlogCommentModel.getById(first_id)
            self._expireCommentCache(first_id)

            for id in ids:
                BlogCommentModel.delete(id)

        return 'ok'

    @amiweb.expose
    @editPermission
    def fetchMore(self, last_id, ident=''):
        last_id = long(last_id)
        list = BlogCommentModel.getAllByIdent(ident, after_id=last_id)
        jsons = [ i.toJSON() for i in list ]
        return '[%s]' % (','.join(jsons))

    @amiweb.expose
    def getCommentContent(self, id):
        comment = BlogCommentModel.getById(id)
        return comment.content

    @amiweb.expose
    @editPermission
    def updateComment(self, id, content):
        BlogCommentModel.update(id, content)
        self._expireCommentCache(id)
        return getFormatManager().noPluginFormat(content, True)

BlogController = BlogController()


class SpamComment(Exception):
    pass
