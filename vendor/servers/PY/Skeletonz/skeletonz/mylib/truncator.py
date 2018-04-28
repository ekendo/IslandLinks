import re
def minify(files, extra_static_data, output_file):
    full_text = []
    p1 = re.compile("^//.*")
    seen_slash_star = False

    for f in files:
        for line in open(f, "r").readlines():
            t = line.lstrip()
            if t.find("/*") == 0:
                seen_slash_star = True
            else:
                if not seen_slash_star:
                    t = re.sub(p1, "", t)
                    if t != "\n":
                        full_text.append(t)

            if t.find("*/") != -1:
                seen_slash_star = False

    full_text.extend(extra_static_data)
    open(output_file, "w").writelines(full_text)
