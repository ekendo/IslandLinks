import traceback
def ppError(caption, expl):
    print "\n%s\n%s:" % (caption.upper(), expl)
    traceback.print_exc()
