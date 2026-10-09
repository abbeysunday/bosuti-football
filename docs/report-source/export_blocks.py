# -*- coding: utf-8 -*-
"""Runs a report's content module against a recording back end and writes docx_build/blocks.json.

The Word version is then produced by build_docx.js. Usage:  python export_blocks.py [sem]
Diagrams (ReportLab drawings) and generated images are saved as PNG files next to blocks.json.
"""
import html
import importlib
import json
import os
import sys
from html.parser import HTMLParser

from reportlab.graphics import renderPDF
from reportlab.graphics.shapes import Drawing
from reportlab.lib.units import inch

HERE = os.path.dirname(os.path.abspath(__file__))
OUT_DIR = os.path.join(HERE, "docx_build")
sys.path.insert(0, HERE)
os.makedirs(OUT_DIR, exist_ok=True)

# The diagram functions live in build_doc.py, which builds a PDF on import; load just its definitions.
sys.argv = [sys.argv[0]] + sys.argv[1:]
_src = open(os.path.join(HERE, "build_doc.py"), encoding="utf-8").read().split("# --------------------------------------------------------------------------- build")[0]
diagrams = {"__file__": os.path.join(HERE, "build_doc.py"), "__name__": "build_doc"}
exec(compile(_src, "build_doc.py", "exec"), diagrams)
C = diagrams["C"]


# --------------------------------------------------------------------------- inline markup -> runs
class Runs(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.runs, self.bold, self.italic, self.small = [], 0, 0, 0

    def handle_starttag(self, tag, attrs):
        if tag == "b": self.bold += 1
        elif tag == "i": self.italic += 1
        elif tag == "font": self.small += 1
        elif tag == "br": self.runs.append({"break": True})

    def handle_endtag(self, tag):
        if tag == "b": self.bold -= 1
        elif tag == "i": self.italic -= 1
        elif tag == "font": self.small -= 1

    def handle_data(self, data):
        if data:
            self.runs.append({"text": data, "bold": self.bold > 0, "italic": self.italic > 0, "small": self.small > 0})


def runs(text):
    p = Runs()
    p.feed(str(text))
    p.close()
    return p.runs


# --------------------------------------------------------------------------- recording back end
blocks = []
counter = [0]


def save_png(obj):
    counter[0] += 1
    path = os.path.join(OUT_DIR, f"img{counter[0]:02d}.png")
    if isinstance(obj, Drawing):
        import pypdfium2 as pdfium
        pdf_path = path[:-4] + ".pdf"
        renderPDF.drawToFile(obj, pdf_path)
        pdf = pdfium.PdfDocument(pdf_path)
        pdf[0].render(scale=220 / 72).to_pil().convert("RGB").save(path)
        pdf.close()
        os.remove(pdf_path)
        w_in = obj.width / 72.0
    else:  # FakeImage holding a BytesIO
        from PIL import Image as PILImage
        obj.buf.seek(0)
        PILImage.open(obj.buf).save(path)
        w_in = obj.width / 72.0
    from PIL import Image as PILImage
    iw, ih = PILImage.open(path).size
    return path, min(w_in, 6.0), ih / iw


class FakeImage:
    def __init__(self, buf, width=None, height=None, kind=None):
        self.buf, self.width, self.height = buf, width or 6 * inch, height


class FakeTable:
    def __init__(self, data, colWidths=None, **kw):
        self.data, self.colWidths = data, colWidths

    def setStyle(self, *a, **k):
        pass


class Marker:
    def __init__(self, kind):
        self.kind = kind


class Story(list):
    """Converts anything appended by the content modules into blocks."""

    def append(self, item):
        if isinstance(item, dict):
            blocks.append(item)
        elif isinstance(item, Marker):
            blocks.append({"t": item.kind})
        elif isinstance(item, FakeTable):
            blocks.append({"t": "plaintable", "rows": [[str(c) for c in r] for r in item.data], "widths": [w / inch for w in item.colWidths]})
        elif isinstance(item, FakeImage):
            path, w, ratio = save_png(item)
            blocks.append({"t": "image", "path": path, "width": w, "ratio": ratio})
        elif isinstance(item, Para):
            blocks.append(item.block)
        elif isinstance(item, Pre):
            blocks.append({"t": "code", "lines": item.text.split("\n")})
        elif isinstance(item, (Spacer,)):
            blocks.append({"t": "space", "pt": item.h})
        elif isinstance(item, (TOC,)):
            blocks.append({"t": item.kind})
        # PageBreak / CondPageBreak / NextPageTemplate handled through Marker or ignored

    def extend(self, items):
        for i in items:
            self.append(i)

    def clear(self):
        blocks.clear()

    def __iter__(self):
        return iter(list(blocks))

    def __len__(self):
        return len(blocks)


class Snapshot(list):
    pass


class Para:
    def __init__(self, text, style=None, **kw):
        name = getattr(style, "name", style) or "body"
        self.block = {"t": "p", "style": name, "runs": runs(text)}


class Pre:
    def __init__(self, text, style=None):
        self.text = text


class Spacer:
    def __init__(self, w, h):
        self.h = h


class TOC:
    def __init__(self, formatter=None, kind="toc"):
        self.kind = kind
        self.levelStyles, self.dotsMinLevel = [], 0


def ListOf(kind, formatter=None):
    return TOC(kind={"TabEntry": "lot", "FigEntry": "lof"}[kind])


story = Story()


def body_snapshot():
    """content.build() copies the story, clears it and re-adds it; keep that working with blocks."""


def P(text, style="body"):
    blocks.append({"t": "p", "style": style, "runs": runs(text)})


def bullets(items, style="bullet"):
    for it in items:
        blocks.append({"t": "li", "kind": "bullet", "runs": runs(it)})


def numbered(items):
    blocks.append({"t": "restart"})
    for it in items:
        blocks.append({"t": "li", "kind": "number", "runs": runs(it)})


def chapter(number_word, title):
    blocks.append({"t": "section", "kind": "main"})
    blocks.append({"t": "chapter", "number": number_word, "title": title})


def front_heading(title, toc=True):
    blocks.append({"t": "pagebreak"})
    blocks.append({"t": "front", "title": title, "toc": toc})


def toc_para(text, style, level, toc_text=None):
    return Marker_front(text)


class Marker_front(Marker):
    def __init__(self, text):
        self.kind, self.text = "front", text


def h2(text):
    blocks.append({"t": "h2", "text": text})


def h3(text):
    blocks.append({"t": "h3", "text": text})


def table(caption, header, rows, widths, font_size=9.5):
    blocks.append({"t": "table", "caption": "Table " + caption, "header": [runs(h) for h in header],
                   "rows": [[runs(c) for c in r] for r in rows], "widths": [w / inch for w in widths], "font": font_size})


def figure(flowable, caption):
    if isinstance(flowable, dict):  # screenshot
        flowable["caption"] = "Figure " + caption
        blocks.append(flowable)
        return
    path, w, ratio = save_png(flowable)
    blocks.append({"t": "image", "path": path, "width": w, "ratio": ratio, "caption": "Figure " + caption})


def screenshot(path, caption, width=6.1 * inch, crop=None):
    from PIL import Image as PILImage
    w, h = PILImage.open(path).size
    width_in = 6.0
    if width_in * h / w > 7.4:
        width_in = 7.4 * w / h
    blocks.append({"t": "image", "path": path, "width": width_in, "ratio": h / w, "caption": "Figure " + caption})


def code(path, start=None, end=None, title=None):
    with open(os.path.join(diagrams["PROJECT"], path), encoding="utf-8") as f:
        lines = f.read().splitlines()
    lines = lines[(start or 1) - 1:end]
    if title:
        blocks.append({"t": "p", "style": "bodyleft", "runs": [{"text": title, "bold": True}, {"text": f" ({path})", "small": True}]})
    blocks.append({"t": "code", "lines": lines})


def PageBreak():
    return Marker("pagebreak")


def NextPageTemplate(name):
    return {"t": "section", "kind": name}


def CondPageBreak(h):
    return None


class Styles(dict):
    def __getitem__(self, k):
        return k


g = dict(diagrams)
g.update({
    "story": story, "P": P, "bullets": bullets, "numbered": numbered, "h2": h2, "h3": h3, "chapter": chapter,
    "front_heading": front_heading, "table": table, "figure": figure, "screenshot": screenshot, "code": code,
    "toc_para": toc_para, "Paragraph": Para, "Preformatted": Pre, "Spacer": Spacer, "PageBreak": PageBreak,
    "NextPageTemplate": NextPageTemplate, "CondPageBreak": CondPageBreak, "Image": FakeImage, "Table": FakeTable,
    "TableStyle": lambda *a, **k: None, "TableOfContents": TOC, "ListOf": ListOf, "styles": Styles(), "inch": inch,
})


# content.build() does `body = list(story); story.clear(); ...; story.extend(body)` — emulate with blocks.
class _StoryProxy(Story):
    def __iter__(self):
        return iter([_Raw(b) for b in blocks])


class _Raw:
    def __init__(self, b):
        self.b = b


_orig_append = Story.append


def _append(self, item):
    if isinstance(item, _Raw):
        blocks.append(item.b)
    elif isinstance(item, Marker_front):
        blocks.append({"t": "pagebreak"})
        blocks.append({"t": "front", "title": item.text, "toc": True})
    elif isinstance(item, Marker) and item.kind == "pagebreak":
        blocks.append({"t": "pagebreak"})
    elif item is None:
        pass
    else:
        _orig_append(self, item)


Story.append = _append
g["story"] = _StoryProxy()

C.build(g)

meta = {"title": getattr(C, "PDF_TITLE", "Project Documentation"), "author": C.STUDENT_NAME,
        "out": os.path.join(os.path.dirname(HERE), os.path.splitext(getattr(C, "OUT_FILE", "BOUESTI_Sports_Portal_Project_Documentation.pdf"))[0] + ".docx")}
with open(os.path.join(OUT_DIR, "blocks.json"), "w", encoding="utf-8") as f:
    json.dump({"meta": meta, "blocks": blocks}, f, ensure_ascii=False)
print(len(blocks), "blocks;", counter[0], "generated images ->", meta["out"])
