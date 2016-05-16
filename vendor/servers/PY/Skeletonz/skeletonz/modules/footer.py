from amilib.template import render
from skeletonz import server

class Footer:

    def __init__(self, edit_mode=False):
        self.edit_mode = edit_mode
        self.script_datas = []

    def appendScriptData(self, data):
        self.script_datas.append(data)

    def setPage(self, page_obj):
        self.page_obj = page_obj

    def clearScriptData(self):
        self.script_datas = []

    def renderText(self):
        ns = {'footer_obj': self}
        return render("skeletonz/view/general_templates/footer.tmpl", ns)
