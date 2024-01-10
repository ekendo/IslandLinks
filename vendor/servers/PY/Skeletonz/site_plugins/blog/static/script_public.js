var Cookies = {
    get: function(name) {
       if(document.cookie.length>0) {
           var c_start = document.cookie.indexOf(name + "=");
           if(c_start != -1) { 
               c_start = c_start + name.length+1;
               c_end=document.cookie.indexOf(";",c_start);
               if(c_end==-1) 
                   c_end = document.cookie.length;
               return unescape(document.cookie.substring(c_start,c_end));
           } 
       }
       return '';
    },

    set: function(name, value, /*optional*/ expire_days) {
        var str_exp_date = '';
        if(expire_days) {
            var exdate = new Date();
            exdate.setDate(exdate.getDate() + expire_days);
            str_exp_date = ';expires=' + exdate.toGMTString();
        }
        document.cookie = name + "=" + escape(value) + str_exp_date;
    }
}

function fillInCookieInfo() {
    if(Cookies.get('sk_bp_name')) {
        var form = document.getElementById('CMS_CommentForm');

        var name = Cookies.get('sk_bp_name');
        var email = Cookies.get('sk_bp_email');
        var website = Cookies.get('sk_bp_website');

        form.author.value = name;
        form.email.value = email;
        form.website.value = website;
    }
}

if(!Blog)
    var Blog = {};

Blog.postComment_real = function() {
        var form = AJS.$('CMS_CommentForm');
        var name = AJS.$f(form, 'author');
        var email = AJS.$f(form, 'email');
        var website = AJS.$f(form, 'website');

        var comment = AJS.$f(form, 'content');
        var btn_submit = AJS.$('btn_submit');

        btn_submit.disabled = true;

        var d = AJS.getRequest('blog/addComment');
        d.addCallback(function(cmnt_html) {
            Cookies.set('sk_bp_name', name.value, 30);
            Cookies.set('sk_bp_email', email.value, 30);
            Cookies.set('sk_bp_website', website.value, 30);

            var post_comment = $bytc('div', 'CMS_BlogPostComment')[0];
            var flash_div;
            ACN(post_comment, flash_div = DIV({s: 'padding: 10px 0 10px 0'}, B('Comment posted!')));
            AJS.fx.highlight(flash_div, {duration: 5000, onComplete: function() {
                removeElement(flash_div);
            }});


            var cmnts = AJS.$bytc('div', 'CMS_BlogInner')[0];

            var dom = AJS.HTML2DOM(cmnt_html);
            AJS.ACN(cmnts, dom);
            AJS.fx.highlight(dom, {duration: 5000});

            btn_submit.disabled = false;
            comment.value = '';

            //Update count
            var counts = AJS.$bytc('span', 'CMS_CmntLen');
            AJS.map(counts, function(c) {
                AJS.setHTML(c, parseInt(c.innerHTML) + 1);
            });

            var no_cmnt = AJS.$('CMS_noCmnt');
            if(no_cmnt) {
                AJS.setHTML(no_cmnt, '<span class="CMS_CmntLen">1</span> comment so far');
            }
        });
        d.sendReq(AJS.formContents(form));
        return false;
}

Blog.validateCaptcha_real = function() {
    var form = AJS.$('CMS_CommentForm');

    if($f(form, 'author').value == '' || $f(form, 'content').value == '') {
        alert("Name or comment can't be empty.")
        return false;
    }

    var url = AJS.BASE_URL + '/blog/showCaptcha';
    url = url.replace(/([^:])\/\//g, '$1/');
    return GB_show('Are you a human test?', url, 500, 500);
}

Blog.previewComment_real = function() {
    var url = AJS.BASE_URL + '/blog/previewComment';
    url = url.replace(/([^:])\/\//g, '$1/');
    return GB_show('Preview your comment', url, 500, 500);
}

Blog.renderComment = function(holder) {
    var content = AJS.$('comment_content').value;
    var req = AJS.getRequest('blog/renderComment');
    req.addCallback(function(html) {
        holder.innerHTML = html;
    });
    req.sendReq({content: content});
}

Blog.postComment = onDemand("Blog.postComment_real", path_ajs);
Blog.validateCaptcha = onDemand("Blog.validateCaptcha_real", path_ajs);
Blog.previewComment = onDemand("Blog.previewComment_real", path_ajs.concat(path_greybox));
