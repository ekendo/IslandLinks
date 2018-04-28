from skeletonz.server import getConfig
def createToolTip(full_text, text="http://", cls="CMS_hoverAble"):
    return """<span class="%s" onmouseover="AmiTooltip.show(this, null, '%s')" onmouseout="AmiTooltip.hide()">%s</span>""" % (cls, full_text, text)

def createActionLink(inner, image="", elm_onclick="", elm_class="CMS_link", tooltip_inner=False, confirm=None, **kw):
    """
    image: Can be img.png or an relative/absolute link
    elm_onclick: JavaScript onclick
    """
    if confirm != None:
        elm_onclick = "if(confirm('%s')) { %s }" % (confirm, elm_onclick)

    if image != "":
        image = '<img src="%s" alt="" />' % (image)
    if elm_onclick != "":
        elm_onclick = ' onclick="%s"' % elm_onclick

    extra_args = []
    for k in kw:
        extra_args.append('%s="%s"' % (k, kw[k].replace('"', '&#34;')))
    extra_args = ' '.join(extra_args)

    if tooltip_inner:
        res = createToolTip(inner, """<span class="%s"%s %s>%s</span>""" % (elm_class, elm_onclick, extra_args, image), "")
        return res
    else:
        return """<span class="%s"%s>%s%s</span>""" % (elm_class, elm_onclick, image, inner)
