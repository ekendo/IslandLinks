import datetime
import simplejson
import types

class ComplexDecoder(simplejson.JSONDecoder):

    def default(self, obj):
        return simplejson.JSONDecoder.default(self, obj)


class ComplexEncoder(simplejson.JSONEncoder):

    def default(self, obj):
        if isinstance(obj, datetime.date) or isinstance(obj, datetime.datetime):
            return  obj.strftime("%a, %d %b %Y %H:%M:%S GMT")
        return simplejson.JSONEncoder.default(self, obj)

def read(data):
    result = ComplexDecoder().decode(data)
    return result

def write(obj, js_date=True):
    result = simplejson.dumps(obj, ensure_ascii=False, js_date=js_date)
    return result
