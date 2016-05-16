import re
from amiformat import AmiFormat

def removeWS(r1):
    r1 = r1.replace("\n", "")
    r1 = re.sub('>\s+<', '><', r1)
    return r1

def runFormat(text):
    parser = AmiFormat()
    r1 = parser.htmlFormat(text)
    r1 = removeWS(r1)
    return r1


#--- Test formater ----------------------------------------------
text_ml1 = """Test


Test1
Test2

Test3


Test
"""


def test_ml1():
    assert runFormat(text_ml1) ==\
        "<p>Test</p><p>Test1<br />Test2</p><p>Test3</p><p>Test</p>"

text_ml2 = """
Test

Test
Test


cow
"""

def test_ml2():
    assert runFormat(text_ml2) ==\
        "<p>Test</p><p>Test<br />Test</p><p>cow</p>"

text_ml3 = """Test1

Test2

Test3


Test4
"""

def test_ml3():
    assert runFormat(text_ml3) ==\
        "<p>Test1</p><p>Test2</p><p>Test3</p><p>Test4</p>"

text_sl1 = "Hej"

def test_sl1():
    assert runFormat(text_sl1) ==\
        "<p>Hej</p>"

#Test HTML
text_html1 = """
[escape]
<h2>Hej</h2>
<p>Blah</p>
<b>Buuh</b>
[/escape]

Buuh

Test

Jeg tester dette <b>Juhu</b>
"""
def test_html1():
    assert runFormat(text_html1) ==\
        "<h2>Hej</h2><p>Blah</p><b>Buuh</b><p>Buuh</p><p>Test</p><p>Jeg tester dette <b>Juhu</b></p>"

text_html2 = """
Test1 <b>Muuh</b>

[escape]
<h2>Blah</h2>
[/escape]


Test2
"""
def test_html2():
    assert runFormat(text_html2) ==\
        "<p>Test1 <b>Muuh</b></p><h2>Blah</h2><p>Test2</p>"

#Test lister
text_list1 = """
h2. Dette er en test

* Punkt 1
* Punkt 2
* Punkt 3
* Punkt 4

Ny paragraf"""

def test_list1():
    assert runFormat(text_list1) ==\
        "<h2>Dette er en test</h2><ul><li>Punkt 1</li><li>Punkt 2</li><li>Punkt 3</li><li>Punkt 4</li></ul><p>Ny paragraf</p>"

text_list2 = """
h2. Dette er en test

* Punkt 1
Under punkt 1
* Punkt 2
* Punkt 5
Under punkt 5
* Punkt 4

Ny paragraf"""

def test_list2():
    assert runFormat(text_list2) ==\
        "<h2>Dette er en test</h2><ul><li>Punkt 1<br />Under punkt 1</li><li>Punkt 2</li><li>Punkt 5<br />Under punkt 5</li><li>Punkt 4</li></ul><p>Ny paragraf</p>"

text_list3 = """
h2. Dette er en test

* Hej
* TestHej

* Hej

Ny paragraf"""

def test_list3():
    assert runFormat(text_list3) ==\
        "<h2>Dette er en test</h2><ul><li>Hej</li><li>TestHej</li></ul><ul><li>Hej</li></ul><p>Ny paragraf</p>"

#Test links
text_link1 = """
"En link til Google1":(http://google.com/)
"""
def test_link1():
    assert runFormat(text_link1) ==\
        '<p><a href="http://google.com/">En link til Google1</a></p>'


text_link2 = """
* "En link til Google 1":(http://google.com/)
* "En link til Google 2":(http://google.com/)
"""
def test_link2():
    assert runFormat(text_link2) ==\
        '<ul><li><a href="http://google.com/">En link til Google 1</a></li><li><a href="http://google.com/">En link til Google 2</a></li></ul>'

#Test images
text_img1 = "!Hejsa med dig(Buuh)!"
text_img2 = "!http://amix.dk/images/logo.gif(Amix logo)!"
text_img3 = "!http://amix.dk/images/logo.gif(Amix logo)!:(http://amix.dk)"

def test_img1():
    assert runFormat(text_img1) ==\
        '<p>!Hejsa med dig(Buuh)!</p>'

def test_img2():
    print runFormat(text_img2)
    assert runFormat(text_img2) ==\
        '<p><img src="http://amix.dk/images/logo.gif" alt="Amix logo" /></p>'

def test_img3():
    assert runFormat(text_img3) ==\
        '<p><a href="http://amix.dk"><img src="http://amix.dk/images/logo.gif" alt="Amix logo" /></a></p>'

#Test code
text_code1 = """
h3. Juhu

[code]
Amix.foo {
  Buuh
}
[/code]
"""
def test_code1():
    assert runFormat(text_code1) ==\
        '<h3>Juhu</h3><pre>Amix.foo {  Buuh}</pre>'

#Test text styling
text_style1 = """
%{color: red, text-transform: none} Text%

%(b) Hello World%

%(i) Hello World%

%(s) Hello World%

%(mijoo) Hellow%

Dette er en % jahoo. \%
"""

def test_style1():
    assert runFormat(text_style1) ==\
      '<p><span style="color: red; text-transform: none">Text</span></p><p><b>Hello World</b></p><p><i>Hello World</i></p><p><strike>Hello World</strike></p><p><span class="mijoo">Hellow</span></p><p>Dette er en % jahoo. \\%</p>'

text_bug1 = """
"My bookmarks". A GreyBox window should show you my "del.icio.us":(http://del.icio.us/) bookmarks.
"""
def test_bug1():
    assert runFormat(text_bug1) ==\
        '<p>"My bookmarks". A GreyBox window should show you my <a href="http://del.icio.us/">del.icio.us</a> bookmarks.</p>'


text_mixed = """
%(b) %(i) Hello%%
%(b) %{color: red} Hello%%
Hello
"""
def test_mixed():
    assert runFormat(text_mixed) ==\
        '<p><b><i>Hello</i></b><br /><b><span style="color: red">Hello</span></b><br />Hello</p>'

text_bug2 = """
This is a test

[escape]
<ul>
  <li></li>
</ul>
[/escape]

Test
"""
def test_bug2():
    assert runFormat(text_bug2) ==\
        '<p>This is a test</p><ul><li></li></ul><p>Test</p>'

text_bug3 = """
* <br />Test<br />

Hello
* Test
"""
def test_bug3():
    assert runFormat(text_bug3) ==\
        '<ul><li><br />Test<br /></li></ul><p>Hello</p><ul><li>Test</li></ul>'

def test_bug4():
    text = """
* Item 1
* Item 2
"""
    assert runFormat(text) == '<ul><li>Item 1</li><li>Item 2</li></ul>'


#--- Test plugin handling ----------------------------------------------
def test_sl_plugin():
    check =  {}
    def handler(args):
        if args.has_key('data'):
            check['data'] = args['data']
            return True, None
        elif args.has_key('color'):
            check['color'] = args['color']
            return False, None

    parser = AmiFormat()
    parser.registerSLPlugin('ltx', handler)
    parser.registerMLPlugin('latex', handler)
    text = """
h2. My LaTeX
[ltx=2+2, color=red]
[latex] 2+2 [/latex]
[latex]
Test1
Test2
[/latex]
[dummy]"""
    result = removeWS(parser.htmlFormat(text))
    assert check['data'] == 'Test1\nTest2'
    assert check['color'] == 'red'
    assert result == """<h2>My LaTeX</h2><p>[ltx=2+2, color=red]</p><p>[latex] 2+2 [/latex]</p><p>[latex]<br />Test1<br />Test2<br />[/latex]</p><p>[dummy]</p>"""

def test_header_plugin():
    def handler(args):
        return True, ''

    parser = AmiFormat()
    parser.registerMLPlugin('header_data', handler)
    text = """
h2. Test juhu.
[header_data]
<style>
  .feats {
    line-height: 13px;
  }
</style>
[/header_data]

[dummy]

Dette er en test.
Yes sir!
"""
    result = removeWS(parser.htmlFormat(text))
    print result
    assert result == '<h2>Test juhu.</h2><p>[dummy]</p><p>Dette er en test.<br />Yes sir!</p>'

def test_plugins_combined():
    def file_handler(args):
        print args
        return False, None

    def image_handler(args):
        print args
        return False, None

    parser = AmiFormat()
    parser.registerSLPlugin('file', file_handler)
    parser.registerSLPlugin('image', image_handler)

    text = """
[image=Test, show_as=[file=Hellow]]
"""
    parser.htmlFormat(text)

def test_bug5():
    text = """
PROMPT='%{^[[1;32m%}[%{^[[36m%}%n@%m%{^[[1;32m%}]%{^[[00m%}%# '
RPROMPT='%{^[[1;32m%}(%{^[[36m%}%~%{^[[1;32m%})%{^[[00m%}%'
    """
    parser = AmiFormat()
    parser.htmlFormat(text)

def test_escapes():
    text1 = """
[escapestyle]
%(b) Test%
  Test
[/escapestyle]

[escapestyle]%(b) Test%[/escapestyle]

[dummy]"""
    parser = AmiFormat()
    r_text1 = removeWS(parser.htmlFormat(text1))
    assert  r_text1 == '<p>%(b) Test%  Test</p><p>%(b) Test%</p><p>[dummy]</p>'

def test_bug6():
    text = """
[lolly]
pop
[/lolly]

[lolly] pop [/lolly]

[comment]Hello[/comment]"""
    def handler(args):
        return True, '<div>%s</div>' % args['data']
    parser = AmiFormat()
    parser.registerMLPlugin('comment', handler)
    r_text1 = removeWS(parser.htmlFormat(text))
    assert r_text1 == '<p>[lolly]<br />pop<br />[/lolly]</p><p>[lolly] pop [/lolly]</p><div>Hello</div>'

def test_bug7():
    text = """
h2. Example of use

This code:
[hlcode, language=python]
return True, '<div style="color: [escapestyle]%(color)s">%(data)s</div>' %[/escapestyle] args
parser = amiformat.AmiFormat()
parser.registerMLPlugin('comment', commentHandler)
text = "
h2 . Just a test

Here is a red comment:
[!comment, color=red]
I am red
[!/comment]"
print parser.htmlFormat(text)
[/hlcode]
    """
    parser = AmiFormat()
    def handler(args):
        return True, '<div>%s</div>' % args['data']
    parser.registerMLPlugin('comment', handler)
    parser.registerMLPlugin('hlcode', handler)
    r_text1 = removeWS(parser.htmlFormat(text))

def test_bug8():
    text = """Here are some [random_colors=random colors]!"""
    def handler(args):
        return False, 'random colors'

    parser = AmiFormat()
    parser.registerSLPlugin('random_colors', handler)
    r_text = removeWS(parser.htmlFormat(text))
    assert r_text == '<p>Here are some random colors!</p>'

def test_bug9():
    text = """%(hl) Hej%
%(hl) [image=folder, global] my_sk_page%"""
    parser = AmiFormat()
    def handler(args):
        return False, None
    parser.registerSLPlugin('image', handler)
    r_text = removeWS(parser.htmlFormat(text))
    assert r_text == '<p><span class="hl">Hej</span><br /><span class="hl">[image=folder, global] my_sk_page</span></p>'

def test_bug10():
    text = """* Test
[image]"""
    parser = AmiFormat()
    def handler(args):
        return False, None
    parser.registerSLPlugin('image', handler)
    r_text = removeWS(parser.htmlFormat(text))
    assert r_text == '<ul><li>Test<br />[image]</li></ul>'

def test_bug11():
    text = """[[image=Fluffy]]"""
    parser = AmiFormat()
    def handler(args):
        return False, ''
    parser.registerSLPlugin('image', handler)
    r_text = removeWS(parser.htmlFormat(text))
    assert r_text == '<p>[]</p>'


def test_http_links():
    text = """http://amix.dk/

This is a link: http://amix.dk/?dkd

[link=http://amix.dk/]"""
    parser = AmiFormat()
    r_text = removeWS(parser.htmlFormat(text))
    assert r_text == '<p><a href="http://amix.dk/">http://amix.dk/</a></p><p>This is a link: <a href="http://amix.dk/?dkd">http://amix.dk/?dkd</a></p><p>[link=http://amix.dk/]</p>'

def test_http_link_bug():
    parser = AmiFormat()
    text = 'This is a :D buu'
    r_text = removeWS(parser.htmlFormat(text))
    assert r_text == '<p>This is a :D buu</p>'


def test_infinite_loop():
    text = """
CONTENT=[html]
Todoist is now moved to the new data center.

<p>
We have also added some improvements, some of these are:
</p>

<ul>
    <li>Faster hardware, should be able to handle much more users</li>
    <li>Support for <b>q: free text search</b></li>
    <li>Support for back and forward browser buttons</li>

    <li>Improved and upgraded the libraries behind Todoist</li>
</ul>

<p>
If you spot any bugs with the move, please report them on <a href="http://getsatisfaction.com/todoist" target="_blank">http://getsatisfaction.com/todoist</a>
</p>
[/html]

[escape]
Todoist is now moved to the new data center.

<p>
We have also added some improvements, some of these are:
</p>

<ul>
    <li>Faster hardware, should be able to handle much more users</li>
    <li>Support for <b>q: free text search</b></li>
    <li>Support for back and forward browser buttons</li>

    <li>Improved and upgraded the libraries behind Todoist</li>
</ul>

<p>
If you spot any bugs with the move, please report them on <a href="http://getsatisfaction.com/todoist" target="_blank">http://getsatisfaction.com/todoist</a>
</p>
[/html]
"""
    parser = AmiFormat()
    assert parser.htmlFormat(text) != None
