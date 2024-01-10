"""
Sections are extra sections that run after Cheetah and AmiCache. They are made so that some things could be filled in dynamically everytime.

Their syntax is:
  <?= name ?>
"""
import re
from amilib.template import render

from skeletonz.model import CMSModel
from skeletonz.server import getFormatManager

##
# API functions
#
dynamic_sections = {}

def mapSection(name, render_fn):
    dynamic_sections[name] = {'render': render_fn}


def fillSections(current_info, content, page):
    #Dynamic templates
    #for name, dict in dynamic_sections.items():
    #	print "[sections] fillSections: <"
    #	print name
    #	print "><br>"
    #    content = content.replace("<?= %s ?>" % name, dict['render'](current_info))
    #Support for plugins
    #plugin_tags = re.finditer('\[(\[.*?\])\]', content)
    #for m in plugin_tags:
    #    filtered = getFormatManager().htmlFormat(m.group(1), current_info['edit_mode'], False, page)
    #    content = content.replace(m.group(0), filtered)
    return content


##
# The dynamic sections for Skeletonz
#
def initSkeletonzMappings():
    mapSection("cms_js_oracle", renderOracleContent)
    mapSection("cms_link_box", renderLinkBoxContent)

## Oracle
def renderOracleContent(current_info):
    return render("skeletonz/view/d_components/oracle.tmpl", current_info)

##Link (admin) box
def renderLinkBoxContent(current_info):
    return render("skeletonz/view/d_components/link_box.tmpl", current_info)

initSkeletonzMappings()
