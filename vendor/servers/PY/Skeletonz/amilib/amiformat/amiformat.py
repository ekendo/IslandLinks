#!/usr/bin/env python
"""amiformat - Simple textile like web text generator done in Python.

Version: 2.2
Last-Modified: 29/04/07 19:10:08

Copyright (C) 2007 amix - http://amix.dk/"""

import re
import types
import cgi
import traceback

from amiparse import Lexer, Parser, Token
from amiparse import EOT, ParseError

DOMAIN = None


#--- Block lexer ----------------------------------------------
class BlockLexer(Lexer):

    def __init__(self):
        tokens = [
            ('header', r'h([1-6])(\.|!)\ ?'),
            ('list_item', r'\*\s* (.+)'),
            ('line_break', r'\n'),

            ('escapestyle', r'(?P<l_data2>.*?)  (?P<inner> \[escapestyle\] ([^$]*?) \[/escapestyle\] )  (?P<r_data2>.*?)'),
            ('escape', r'\[escape\] ([^$]*?) \[/escape\]'),
            ('code', r'\[code\] ((.|\s)*?) \[/code\]'),

            ('plugin', r'(?P<l_data1>.*?)  (?P<all_tag> \[(?P<p_name2> ((\[.+?\]) | [^\]])+?  )\])  (?P<r_data1>.*?)'),

            ('text', r'.+')
        ]
        Lexer.__init__(self, tokens)
        self.is_plugin = re.compile('\w+([,=]|$)')

    def tokenize(self, text):
        self.ts = []
        Lexer.tokenize(self, text)
        return self.ts

    def header(self, m):
        all = m.group(0)
        if all.find('!') != -1:
            token = Token(type='text', value=all.replace('!', '.'))
        else:
            token = Token(type='header', value=m.group(1))
        self.ts.append(token)

    def list_item(self, m):
        self.ts.append(Token(type='list_item_start'))
        Lexer.tokenize(self, m.group(1))
        self.ts.append(Token(type='list_item_end'))

    def line_break(self, m):
        token = Token(type='line_break', value='\n')
        self.ts.append(token)

    def plugin(self, m):
        #We support escape by [!
        name = m.group('p_name2')
        all_tag = m.group('all_tag')
        if name.find('!') == 0:
            token = Token(type='text', value=all_tag.replace('[!', '[', 1))
        elif name.find('/') == 0:
            token = Token(type='plugin_end', value='[%s]' % name)
        elif self.is_plugin.match(name):
            v = {
                'inner_tag': name,
                'all_tag': all_tag
            }
            token = Token(type='plugin_start', value=v)
        else:
            #It's not a plugin!
            token = None

        Lexer.tokenize(self, m.group('l_data1'))
        if token:
            self.ts.append(token)
        else:
            self.ts.append(Token(type='text', value='['))
            Lexer.tokenize(self, name)
            self.ts.append(Token(type='text', value=']'))
        Lexer.tokenize(self, m.group('r_data1'))

    def escape(self, m):
        print 'her!!'
        self.ts.append(Token(type='escape_start'))
        Lexer.tokenize(self, m.group(1))
        self.ts.append(Token(type='escape_end'))

    def escapestyle(self, m):
        Lexer.tokenize(self, m.group('l_data2'))

        token = Token(type='text', value=m.group('inner'))
        self.ts.append(token)

        Lexer.tokenize(self, m.group('r_data2'))

    def text(self, m):
        token = Token(type='text', value=m.group(0))
        self.ts.append(token)

    def code(self, m):
        token = Token(type='code', value=m.group(1))
        self.ts.append(token)

BLOCK_LEXER = BlockLexer()
def lex_block(text):
    return BLOCK_LEXER.tokenize(text)


#--- Style lexer ----------------------------------------------
class StyleLexer(Lexer):

    def __init__(self):
        img_reg = r'(?P<all_tag%s>  !(?P<i_link%s>[^\s]+)\s*\((?P<alt%s>.*)\)!  )'
        tokens = [
            ('text_style', r'(?P<l_data1>.*?)  %(?P<format>\(.*?\)|{.*?})\  (?P<text>%*.*?%*) %  (?P<r_data1>.*?)'),
            ('links', r'(?P<l_data2>.*?)  (?P<all_tag3>  "(?P<l_text>[^"]+?)":\((?P<l_link>.+?)\)  )  (?P<r_data2>.*?)'),
            ('link_images', r'(?P<l_data4>.*?)  %s:\((?P<page_link>.+?)\)  (?P<r_data4>.*?)' % img_reg % (2, 2, 2)),
            ('images', r'(?P<l_data3>.*?)  %s  (?P<r_data3>.*?)' % img_reg % (1, 1, 1)),
            ('line_break', r'\n'),
            ('data', r'.+')
        ]
        Lexer.__init__(self, tokens)

    def tokenize(self, text):
        self.ts = []
        Lexer.tokenize(self, text)
        return self.ts

    def appendData(self, data):
        if data:
            token = Token(type='data', value=data)
            self.ts.append(token)

    def data(self, m):
        self.appendData(m.group(0))

    def line_break(self, m):
        token = Token(type='line_break', value='\n')
        self.ts.append(token)

    def text_style(self, m):
        Lexer.tokenize(self, m.group('l_data1'))

        format = m.group('format')
        text = m.group('text')

        html_style = re.match('\(([bius])\s*\)', format)
        span_class = re.match('\((\w+)\)', format)
        span_style = re.match('{(\w+.+)}', format)

        token = None
        tag_or_cls = None
        if html_style:
            tag_or_cls = html_style.group(1)
            token = Token(type='html_tag_start', value=tag_or_cls)
        elif span_class:
            tag_or_cls = span_class.group(1)
            token = Token(type='span_class_start', value=tag_or_cls)
        elif span_style:
            span_inner = span_style.group(1)
            try:
                spl = span_inner.split(",")
                style = []
                for item in spl:
                    k, v = item.split(":")
                    v = v.strip()
                    style.append("%s: %s" % (k, v))
                style = ';'.join(style)
                token = Token(type='span_style_start', value=style)
            except:
                token = Token(type='text', value=span_style.group(0))
        else:
            token = Token(type='text', value=format)
        self.ts.append(token)

        #Recursively handle the inner text
        #since we need to support %(b) %(i) Hej%%
        Lexer.tokenize(self, text)

        if html_style:
            token = Token(type='html_tag_end', value=tag_or_cls)
        else:
            token = Token(type='span_end')
        self.ts.append(token)

        Lexer.tokenize(self, m.group('r_data1'))

    def links(self, m):
        Lexer.tokenize(self, m.group('l_data2'))

        v = {'text': m.group('l_text'),
             'link': m.group('l_link'),
             'all_tag': m.group('all_tag3')}
        token = Token(type='link', value=v)
        self.ts.append(token)

        Lexer.tokenize(self, m.group('r_data2'))

    def images(self, m):
        Lexer.tokenize(self, m.group('l_data3'))

        v = {'alt': m.group('alt1'),
             'link': m.group('i_link1'),
             'all_tag': m.group('all_tag1')}
        token = Token(type='image', value=v)
        self.ts.append(token)

        Lexer.tokenize(self, m.group('r_data3'))

    def link_images(self, m):
        Lexer.tokenize(self, m.group('l_data4'))

        v = {'alt': m.group('alt2'),
             'img_link': m.group('i_link2'),
             'page_link': m.group('page_link'),
             'all_tag': m.group('all_tag2')}
        token = Token(type='imagelink', value=v)
        self.ts.append(token)

        Lexer.tokenize(self, m.group('r_data4'))


STYLE_LEXER = StyleLexer()
def lex_style(text):
    return STYLE_LEXER.tokenize(text)


#--- Block parser ----------------------------------------------
class BlockParser(Parser):

    def __init__(self, tokens, plugin_listener):
        self.plugin_listener = plugin_listener
        Parser.__init__(self, tokens)
        self.escape_mode = False

        #From MoinMoin
        url_pattern = 'http|https|ftp|nntp|news|mailto|telnet|wiki|file|irc'
        punct_pattern = re.escape('''"\'}]|:,.)?!''')
        url_rule = r'( |^)%(url_guard)s(%(url)s)\:([^\s\<%(punct)s]|([%(punct)s][^\s\<%(punct)s]))+' % {
            'url_guard': '(^|(?<!\w))',
            'url': url_pattern,
            'punct': punct_pattern,
        }
        self.url_pattern = re.compile(url_rule)

    def parse(self):
        while 1:
            try:
                self.nextToken()
            except EOT:
                break

            if self.ct.type == 'escape_start':
                self.escape_mode = True
            elif self.ct.type == 'escape_end':
                self.escape_mode = False
            elif self.ct.type == 'code':
                self.parseCode()
            elif self.ct.type == 'header':
                self.parseHeader()
            elif self.ct.type == 'list_item_start':
                self.parseList()
            elif self.ct.type == 'line_break':
                self.line_pos += 1
            elif self.ct.type == 'plugin_start':
                self.parsePara()
            elif self.ct.type == 'text':
                self.parsePara()

    def parseHeader(self):
        size = self.ct.value
        text = []
        while 1:
            if self.checkNextToken('text'):
                self.nextToken()
                text.append(self.ct.value)
            elif self.checkNextToken('plugin_start'):
                self.nextToken()
                block_plugin, output = self.parsePluginStart()
                text.extend(output)
            else:
                break
        self.output.append('<h%s>%s</h%s>\n' %
                (size, ''.join(text), size))

    def parseList(self):
        self.output.append('<ul>\n')
        while 1:
            self.output.append('<li>\n')
            while 1:
                # Support following
                # * Item 1 [plugin] text
                #   Under item
                # * Item 2
                if self.checkNextToken('list_item_end'):
                    self.nextToken()
                    if not self.checkNextToken('line_break'):
                        break
                elif self.checkNextToken('line_break'):
                    self.nextToken()
                    if self.checkNextToken('text'):
                        self.output.append('<br />\n')
                    elif self.checkNextToken('plugin_start'):
                        self.output.append('<br />\n')
                    else:
                        break
                elif self.checkNextToken('text'):
                    self.nextToken()
                    self.output.append(self.ct.value)
                elif self.checkNextToken('plugin_start'):
                    self.nextToken()
                    block_plugin, output = self.parsePluginStart()
                    self.output.extend(output)
                else:
                    break
            self.output.append('\n</li>\n')

            if self.checkNextToken('list_item_start'):
                self.nextToken()
            else:
                break
        self.output.append('</ul>\n')

    def parseText(self, text):
        #HTTP addr.
        def change(mo):
            full_link = mo.group(0).strip()

            if len(full_link) > 30:
                link = '%s...' % full_link[0:30]
            else:
                link = full_link

            url = mo.group(1)

            return '%s<a href="%s">%s</a>' % (url, full_link, link)
        text = self.url_pattern.sub(change, text)
        return text

    def parsePara(self):
        #XXX:
        # If we have a block plugin, then we put it outside <p>...</p>
        # If we dont have a block plugin, then we treat it like text
        if not self.escape_mode:
            self.output.append('\n<p>\n')

        block_plugin = False
        while 1:
            if self.ct.type == 'text':
                value = self.parseText(self.ct.value)
                self.output.append(value)
            elif self.ct.type == 'line_break':
                if self.checkNextToken('text'):
                    if not self.escape_mode:
                        self.output.append('<br />\n')
                elif self.checkNextToken('plugin_end'):
                    if not self.escape_mode:
                        self.output.append('<br />\n')
                    self.output.append(self.ct.value)
                else:
                    self.index -= 1
                    break
            elif self.ct.type == 'plugin_start':
                block_plugin, result = self.parsePluginStart()
                if block_plugin and not self.escape_mode:
                    if self.output[-1] == '\n<p>\n':
                        self.output.pop()
                    else:
                        self.output.append('\n</p>\n')
                    self.output.extend(result)
                    break
                else:
                    self.output.extend(result)
            elif self.ct.type == 'plugin_end':
                self.output.append(self.ct.value)
            else:
                self.index -= 1
                break

            try:
                self.nextToken()
            except EOT:
                break

        if not block_plugin:
            if not self.escape_mode:
                self.output.append('\n</p>\n')

    def parseCode(self):
        self.output.append('\n<pre>')
        self.output.append(cgi.escape(self.ct.value))
        self.output.append('</pre>\n')

    def _tagParse(self, tag_inner):
        #XXX:
        #  Hack to support inner plugins
        inner_plugins = {}
        i = 0
        for m in re.finditer('\[(.+?)\]', tag_inner):
            key = '!x%ix!' % i
            val = m.group(1)
            inner_plugins[key] = val
            tag_inner = tag_inner.replace(m.group(0), key)
            i += 1

        k_ws = re.split(",\s*", tag_inner)

        m_tag = ''
        args = {}
        is_first = True
        for kw in k_ws:
            kw = kw.split("=")
            if is_first:
                m_tag = kw[0]

            if len(kw) == 2:
                #The assigned could be another plugin
                #we must reparse it
                if kw[1] in inner_plugins:
                    p_tag, p_args = self._tagParse(inner_plugins[kw[1]])
                    args[kw[0]] = {
                        'plugin_type': p_tag,
                        'plugin_args': p_args
                    }
                else:
                    args[kw[0]] = kw[1]
            else:
                if is_first:
                    args[kw[0]] = None
                else:
                    args[kw[0]] = ''
            is_first = False

        return m_tag, args

    def _convertInnerPluginsToData(self, data):
        """Special case for [k].

        We concider those as plugins, so we need to convert them to tags.
        """
        daty = []
        for v in data:
            if type(v) == types.DictType:
                daty.append(v['all_tag'])
            else:
                daty.append(v)
        return daty

    def parsePluginStart(self):
        tag, args = self._tagParse(self.ct.value['inner_tag'])
        all_tag = self.ct.value['all_tag']

        result = None
        block_plugin = False

        #Remember start so we can revert back
        #we jump ahead of current plugin start
        start_index = self.index

        is_sl_plugin = self.plugin_listener.isSLPlugin(tag)
        is_ml_plugin = self.plugin_listener.isMLPlugin(tag)

        if is_sl_plugin:
            try:
                block_plugin, result = self.plugin_listener.pluginSLParsed(tag, args)
            except Exception, e:
                traceback.print_exc()
                print 'AmiFormat ERROR - "%s" plugin raised:' % tag
                print '  (%s) %s' % (e.__class__.__name__, e)
        elif is_ml_plugin:
            #Skip the first line break
            if self.checkNextToken('line_break'):
                self.skipToken('line_break')

            #Search until end tag is found
            data = []
            while 1:
                if self.checkNextToken('plugin_end'):
                    self.nextToken()
                    break
                elif self.getNextToken() == None:
                    break
                else:
                    self.nextToken()
                    v = self.ct.value or ''
                    data.append(v)

                    if self.checkNextToken('plugin_end'):
                        self.nextToken()
                        break

            if is_ml_plugin:
                args['data'] = ''.join(self._convertInnerPluginsToData(data)).strip()
                try:
                    block_plugin, result = self.plugin_listener.pluginMLParsed(tag, args)
                except Exception, e:
                    traceback.print_exc()
                    print 'AmiFormat ERROR - "%s" plugin raised:' % tag
                    print '  (%s) %s' % (e.__class__.__name__, e)

        if result == None:
            result = all_tag
            block_plugin = False
            self.index = start_index
        result = ['[escapestyle]', result, '[/escapestyle]']
        return block_plugin, result


#--- Style parser ----------------------------------------------
class StyleParser(Parser):

    def __init__(self, tokens):
        Parser.__init__(self, tokens)
        self.escape_mode = False

    def parse(self):
        while 1:
            try:
                self.nextToken()
            except EOT:
                break

            if self.ct.type == 'escape':
                self.output.append(self.ct.value)

            elif self.ct.type == 'data':
                self.parseData()
            elif self.ct.type == 'line_break':
                self.line_pos += 1
                self.output.append('\n')

            elif self.ct.type == 'html_tag_start':
                self.parseHtmlTagStart()
            elif self.ct.type == 'html_tag_end':
                self.parseHtmlTagEnd()
            elif self.ct.type == 'span_class_start':
                self.parseSpanClass()
            elif self.ct.type == 'span_style_start':
                self.parseSpanStyle()
            elif self.ct.type == 'span_end':
                self.parseSpanEnd()

            elif self.ct.type == 'link':
                self.parseLink()
            elif self.ct.type == 'imagelink':
                self.parseImageLink()
            elif self.ct.type == 'image':
                self.parseImage()

    def parseData(self):
        self.output.append(self.ct.value)

    def parseHtmlTagStart(self):
        tag = self.ct.value
        if tag == 's':
            tag = 'strike'
        self.output.append('<%s>' % tag)

    def parseHtmlTagEnd(self):
        tag = self.ct.value
        if tag == 's':
            tag = 'strike'
        self.output.append('</%s>' % tag)

    def parseSpanClass(self):
        self.output.append('<span class="%s">' % (self.ct.value))

    def parseSpanStyle(self):
        self.output.append('<span style="%s">' % (self.ct.value))

    def parseSpanEnd(self):
        self.output.append('</span>')

    def parseLink(self):
        link = self.ct.value['link']
        text = self.ct.value['text']

        target = ''
        if DOMAIN and link.find(DOMAIN) == -1:
            target = ' target="_blank"'

        html = '<a href="%s"%s>%s</a>' % (link, target, text)
        self.output.append(html)

    def parseImage(self):
        alt = self.ct.value['alt']
        link = self.ct.value['link']
        html = '<img src="%s" alt="%s" />' % (link, alt)
        self.output.append(html)

    def parseImageLink(self):
        alt = self.ct.value['alt']
        img_link = self.ct.value['img_link']
        page_link = self.ct.value['page_link']
        html = '<a href="%s"><img src="%s" alt="%s" /></a>' % (page_link, img_link, alt)
        self.output.append(html)

def parser_style(tokens):
    parser = StyleParser(tokens)
    parser.parse()
    result = parser.getOutput()
    return result


class PluginListener:

    def isMLPlugin(self):
        raise Exception('Not implemented')

    def isSLPlugin(self):
        raise Exception('Not implemented')

    def pluginSLParsed(self, tag, args):
        raise Exception('Not implemented')

    def pluginMLParsed(self, tag, args):
        raise Exception('Not implemented')




class AmiFormat(PluginListener):

    def __init__(self):
        self.sl_plugins = {}
        self.ml_plugins = {}
        self.deco_handler = None

        self.registerMLPlugin('quote', self.quoteHandler)

    def registerSLPlugin(self, tag, handler_fn):
        """Register a single line plugin.

        A single line plugin looks like this:
            [ltx=2+2, color=red]
        You would call registerSLPlugin('ltx', handler_fn).
        handler_fn should be a function that takes a dictionary,
        in the above example following dict would be sent to handler_fn:
            {'ltx': '2+2', 'color': 'red'}
        """
        self.sl_plugins[tag] = handler_fn

    def registerMLPlugin(self, tag, handler_fn):
        """Register a multi line plugin.

        A multi line plugin looks like this:
            [script_data=MyScrip]
            alert('hello');
            [/script_data]
        You would call registerMLPlugin('script_data', handler_fn).
        handler_fn should be a function that takes a dictionary,
        in the above example following dict would be sent to handler_fn:
            {'script_data': 'MyScrip', 'data': 'alert('hello');'}
        """
        self.ml_plugins[tag] = handler_fn

    def _parser_block(self, tokens):
        parser = BlockParser(tokens, self)
        parser.parse()
        return parser.getOutput()

    def htmlFormat(self, text):
        """Parses and transforms some AmiFormatted text into HTML.
        """
        txt_blocks = self._parser_block(lex_block(text))

        #XXX: Maybe there is a better solution, but I doubt
        #The problem is nested escapestyles
        escape_d = {}
        escapes = re.compile('\[escapestyle\] \s* (?P<inner>(.|\s)*?) \s* \[/escapestyle\]', re.VERBOSE)
        def rem(mo):
            h_code = hash(mo.group(0))
            escape_d[h_code] = mo.group('inner')
            return '(<!%s!>)' % h_code
        txt_blocks = escapes.sub(rem, txt_blocks)

        txt_style = parser_style(lex_style(txt_blocks))

        eess = re.compile('\(<!(-?\d+)!>\)')
        def back(mo):
            val = int(mo.group(1))
            if escape_d.has_key(val):
                return escape_d[val]
            return mo.group(0)
        txt_style = eess.sub(back, txt_style)

        return txt_style


    #--- PluginListener implementation ----------------------------------------------
    def isSLPlugin(self, tag):
        return self.sl_plugins.has_key(tag)

    def isMLPlugin(self, tag):
        return self.ml_plugins.has_key(tag)

    def pluginSLParsed(self, tag, args):
        if self.isSLPlugin(tag):
            return self.sl_plugins[tag](args)

    def pluginMLParsed(self, tag, args):
        if self.isMLPlugin(tag):
            return self.ml_plugins[tag](args)

    #--- Internal handlers ----------------------------------------------
    def quoteHandler(self, data, *kw):
        html = self.htmlFormat(data['data'], None)
        return False, '\n<blockquote>\n%s\n</blockquote>\n' % html
