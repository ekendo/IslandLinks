import re
import types
from errors import ppError

DATA_TAGS = []

def _passToFilterFn(fn, args, edit_mode, page):
    try:
        return fn(args, edit_mode, page)
    except:
        ppError("Plugin rendering error", "'%s' returned following error" % fn)

SINGLE_TAG = re.compile('\[((?:[^\[\]]|\n)*?)\]')
SINGLE_TAG_COMB = re.compile('\[((?:.|\n)*?)\]')

"""
Splits the text to find [keyword...]. Runs the filters and returns the result
text: String
filters: [("keyword1", call_back_func1), ..., ("keywordN", call_back_funcN)]
"""
def splitter(text, filters=[], combinators=[], edit_mode=False, page=None):
    old_text = text
    result_text = tag_data_parser(text, filters, edit_mode, page)

    #Run the filters
    changes = {}
    count = 0
    for m in SINGLE_TAG.finditer(text):
        #Extract keyword and arguments
        inner = m.groups()[0]
        keyword, args = tag_parser(inner)

        #Run the function on filters
        change = None
        for f in filters:
            if f[0] == keyword:
                change = _passToFilterFn(f[1], args, edit_mode, page)

                if change != None:
                    changes["!~%i!~" % count] = change
                    result_text = result_text.replace("[%s]" % inner, "!~%i!~" % count)
                    count += 1

    #Run on combinators
    for m in SINGLE_TAG_COMB.finditer(result_text):
        #Extract keyword and arguments
        inner = m.groups()[0]
        keyword, args = tag_parser(inner)

        change = None

        try:
            for arg in args:
                if args[arg].find("!~") == 0:
                    args[arg] = changes[args[arg]]
        except KeyError:
            pass

        for f in combinators:
            if f[0] == keyword:
                change = _passToFilterFn(f[1], args, edit_mode, page)

                if change != None:
                    result_text = result_text.replace("[%s]" % inner, change)

    #Change to real values
    for k in changes.keys():
        ch = changes[k]
        result_text = result_text.replace(k, ch)

    return result_text


def tag_parser(tag_inner):
    """
    Looks for tags in the line, parses it and returns:
      [('tag1', args1), ('tagN', argsN)]
    Args is a dictionary:
      [tag=Text] -> {'tag': 'Text'}
      [tag, key1=Value1, ..., keyN=ValueN] -> {'key1': 'Value1', 'keyN': 'ValueN'}
    """
    k_ws = re.split(",\s*", tag_inner)

    m_tag = ''
    args = {}
    i = 0
    for kw in k_ws:
        kw = kw.split("=")
        if i == 0:
            m_tag = kw[0]
        if len(kw) == 2:
            args[kw[0]] = kw[1]
        else:
            args[kw[0]] = ''
        i += 1
    return m_tag, args


def tag_data_parser(text, filters, edit_mode, page):
    """
    Data tag properites:
      * Single line [tag]DATA[/tag]
      * Multi line [tag]\n\nDATA\n[/tag]
      * They can't have nested tags
    """
    result_text = text
    x = "\[(%s.*?)\]((?:.|\n)*?)\[/%s\]"
    for tag in DATA_TAGS:
        p = re.compile(x % (tag, tag))

        for m in p.finditer(text):
            whole = m.group(0)
            first_tag = m.group(1)
            data = m.group(2)

            args = tag_parser(first_tag)[1]
            args['data'] = data
            args['hack_old_text'] = page.content

            for f in filters:
                if f[0] == tag:
                    change = _passToFilterFn(f[1], args, edit_mode, page)

                    if change != None:
                        result_text = result_text.replace(whole, change)
    return result_text
