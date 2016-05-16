from skeletonz.modules.plugin import GenericPlugin
from skeletonz.server import getFormatManager

PLUGINS_FOR_EXPORT = ['Comment']

COLORS = {
  'red': '#ffaeae',
  'orange': '#fbd489',
  'yellow': '#fbfb89',
  'green': '#89fb94',
  'blue': '#aecbff',
  'violet': '#ffaefe',
  'grey': '#ccc'
}

class Comment(GenericPlugin):
    NAME = "Comment plugin"
    DESCRIPTION = "Can be used to comment text - comments will not be rendered."
    SYNTAX = [
        {'handler': 'cmt',
         'required_arguments': {'ident': 'The comment'},
         'optional_arguments': {'color': 'The color of the comment. Default is yellow.<br /> Can be red, orange, green, blue, violet.'}
        },

        {'handler': 'comment',
         'required_arguments': {'data': {'type': 'text', 'help': 'The comment'}},
         'optional_arguments': {'color': 'The color of the comment. Default is yellow.<br /> Can be red, orange, green, blue, violet.'}
        }
      ]

    def __init__(self):
        getFormatManager().registerSLPlugin('cmt', self.handle)
        getFormatManager().registerMLPlugin('comment', self.handle)

    def handle(self, args, edit_mode, page_id):
        if edit_mode:
            color = COLORS.get(args.get('color', 'yellow'), '#ffffcc')
            if args.has_key('cmt') and args['cmt']:
                return False, """<span style="background-color: %s">%s</span>""" % (color, args['cmt'])
            if args.has_key('comment') and args.has_key('data'):
                data = args['data'].replace('\n', '<br />')
                return True, """<div style="background-color: %s">%s</div>""" % (color, data)
        return True, ''
