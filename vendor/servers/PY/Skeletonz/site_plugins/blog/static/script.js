var Blog = {
    viewAdd: function(ident, elm) {
        return GB_showFullScreen('Add post', PageOracle.getBaseURL() + '/blog/viewAdd?ident=' + ident);
    },

    viewEdit: function(ident, elm) {
        var post = AJS.getParentBytc(elm, "div", "CMS_BlogEntry");
        var vars = 'id=' + Blog._getPostId(post.id);
        return GB_showFullScreen('Edit post', PageOracle.getBaseURL() + '/blog/viewEdit?'+vars);
    },

    viewLabelManager: function(ident, elm) {
        var post = AJS.getParentBytc(elm, "div", "CMS_BlogEntry");
        var update = function() {
            Blog.updateBlog(ident);
        };
        return GB_show('Edit post', PageOracle.getBaseURL() + '/blog/viewLabelManager?ident='+ident, 500, 500, update);
    },

    viewCommentManager: function(ident, elm) {
        return GB_showFullScreen('View all comments', PageOracle.getBaseURL() + '/blog/viewCommentManager?ident='+ident);
    },

    del: function(elm, ident) {
        Indicator.append(elm);
        var post = AJS.getParentBytc(elm, "div", "CMS_BlogEntry");
        var d = AJS.getRequest("blog/delete");
        d.addCallback(function() {
            if(window.location.toString().indexOf('viewEntry/') != -1) {
                var removed = DIV(B('Blog post is now removed.'));
                swapDOM(post, removed);
            }
            else {
                removeElement(post);
            }

            AmiTooltip.hide();
        });
        d.sendReq({id: Blog._getPostId(post.id)});
    },

    updateBlog: function(ident) {
        var elm = AJS.$('Blog_' + ident);
        var fn = function(html) {
            var d = AJS.HTML2DOM(html);
            AJS.swapDOM(elm, d);
            try { GB_hide(); }
            catch(e) { }
        };

        var d = getRequest('blog/getHTMLData');
        d.addCallback(fn);
        d.sendReq({'ident': ident});
    },

    updatePost: function(id) {
        var elm = AJS.$('BlogEntry_' + id);
        var fn = function(html) {
            var d = AJS.HTML2DOM(html);
            AJS.swapDOM(elm, d);
            try { GB_hide(); }
            catch(e) { }
        };
        var d = AJS.getRequest('blog/getPostHTMLData');
        d.addCallback(fn);
        d.sendReq({'id': id, 'is_permanent': elm.is_permanent || 0});
    },

    updateBlogInfo: function() {
        var blog_info = AJS.$('CMS_BlogInfo');
        if(blog_info) {
            var d = AJS.getRequest('blog/getBlogInfoContent');
            d.addCallback(function() {

            });
            d.sendReq
        }
    },

    publish: function(elm, ident) {
        Indicator.append(elm);
        var img = AJS.$bytc('img', null, elm)[0];
        var post = AJS.getParentBytc(elm, "div", "CMS_BlogEntry");
        var d = AJS.getRequest("blog/flipPublish");
        d.addCallback(function(published) {
            Indicator.remove(elm);
            if(published == '1')
                img.src = 'static_core/images/on.png';
            else
                img.src = 'static_core/images/off.png';
        });
        d.sendReq({id: Blog._getPostId(post.id)});
    },

    deleteComment: function(id, elm) {
        Indicator.append(elm);
        var d = AJS.getRequest("blog/deleteComment");
        d.addCallback(function(post_id) {
            Indicator.remove(elm);
            AmiTooltip.hide();
            Blog.updatePost(post_id);
        });
        d.sendReq({id: id});
    },

    editComment: function(id, elm) {
        Indicator.append(elm);

        var dd = AJS.getNextSiblingBytc(AJS.getParentBytc(elm, "dt"), "dd");
        var d = AJS.getRequest("blog/getCommentContent");
        d.addCallback(function(content) {
            Indicator.remove(elm);
            AJS.hideElement(elm);

            var textarea = AJS.TEXTAREA({'class': 'CMS_Comment_TA'});
            textarea.value = content;
            AJS.RCN(dd, textarea);

            var btn_save = AJS.SPAN({'class': 'CMS_link CMS_critical_link'}, AJS.IMG({src: 'static_core/images/save.png'}));
            AmiTooltip.showTooltip(btn_save, null, 'Save changes');
            AJS.insertAfter(btn_save, elm.parentNode);

            AJS.AEV(btn_save, 'click', function() {
                var d1 = AJS.getRequest("blog/updateComment");
                d1.addCallback(function(new_val) {
                    AJS.removeElement(btn_save);
                    AJS.showElement(elm);
                    AJS.RCN(dd, AJS.setHTML(AJS.SPAN(), new_val));
                });
                d1.sendReq({id: id, content: textarea.value});
            });
        });
        d.sendReq({id: id});
    },

    _getPostId: function(id) {
        return id.split('_')[1];
    }

}
