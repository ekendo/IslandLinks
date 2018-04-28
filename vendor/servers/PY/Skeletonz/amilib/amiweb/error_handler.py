from paste.request import resolve_relative_url

def returnHttpError(error_object, environ, start_response):
    code = error_object.code
    title = error_object.title
    explanation = error_object.explanation

    try:
        location = error_object.args[0]
    except:
        location = ""

    headers = []
    headers.append(('Content-Type', 'text/html'))
    if code == 302:
        if location != "":
            location = resolve_relative_url(location, environ)
            headers.append(('Location', location))
        else:
            code = 404
            title = 'Not Found'
            explanation = ('The resource could not be found.')

    start_response('%i %s' % (code, title), headers)
    return [explanation]
