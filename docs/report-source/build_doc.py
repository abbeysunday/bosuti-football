# -*- coding: utf-8 -*-
"""Builds the final-year project documentation PDF for the BOUESTI Sports Portal."""
import os
from io import BytesIO

from PIL import Image as PILImage
from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_JUSTIFY, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.units import cm, inch
from reportlab.platypus import (BaseDocTemplate, Frame, Image, KeepTogether, NextPageTemplate, PageBreak,
                                PageTemplate, Paragraph, Preformatted, Spacer, Table, TableStyle, CondPageBreak)
from reportlab.platypus.tableofcontents import TableOfContents
from reportlab.graphics.shapes import Drawing, Rect, String, Line, Polygon, Circle, Ellipse
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont

import importlib
import sys

# Which report to build: `python build_doc.py` (BOUESTI report) or `python build_doc.py sem` (Sports Event Management report).
C = importlib.import_module("sem_content" if "sem" in sys.argv[1:] else "content")

SCRATCH = os.path.dirname(os.path.abspath(__file__))
PROJECT = os.path.abspath(os.path.join(SCRATCH, "..", ".."))
OUT = os.path.join(PROJECT, "docs", getattr(C, "OUT_FILE", "BOUESTI_Sports_Portal_Project_Documentation.pdf"))

GREEN = colors.HexColor("#0a5c36")
GOLD = colors.HexColor("#b8892b")
GREY = colors.HexColor("#555555")
LIGHT = colors.HexColor("#eef4f0")

# Times (built in) for an academic look; Courier for code.
BODY_FONT, BOLD_FONT, ITALIC_FONT = "Times-Roman", "Times-Bold", "Times-Italic"

styles = {
    "body": ParagraphStyle("body", fontName=BODY_FONT, fontSize=12, leading=18, alignment=TA_JUSTIFY, spaceAfter=8),
    "bodyleft": ParagraphStyle("bodyleft", fontName=BODY_FONT, fontSize=12, leading=18, alignment=TA_LEFT, spaceAfter=6),
    "bullet": ParagraphStyle("bullet", fontName=BODY_FONT, fontSize=12, leading=17, alignment=TA_JUSTIFY, leftIndent=22, bulletIndent=8, spaceAfter=3),
    "chapter": ParagraphStyle("chapter", fontName=BOLD_FONT, fontSize=15, leading=22, alignment=TA_CENTER, spaceAfter=4, textColor=GREEN),
    "chaptertitle": ParagraphStyle("chaptertitle", fontName=BOLD_FONT, fontSize=15, leading=22, alignment=TA_CENTER, spaceAfter=18),
    "h2": ParagraphStyle("h2", fontName=BOLD_FONT, fontSize=12.5, leading=18, spaceBefore=10, spaceAfter=6, textColor=GREEN),
    "h3": ParagraphStyle("h3", fontName=BOLD_FONT, fontSize=12, leading=17, spaceBefore=6, spaceAfter=4),
    "front": ParagraphStyle("front", fontName=BOLD_FONT, fontSize=14, leading=20, alignment=TA_CENTER, spaceAfter=16),
    "caption": ParagraphStyle("caption", fontName=ITALIC_FONT, fontSize=10.5, leading=14, alignment=TA_CENTER, spaceBefore=4, spaceAfter=12, textColor=GREY),
    "cell": ParagraphStyle("cell", fontName=BODY_FONT, fontSize=9.5, leading=12),
    "cellb": ParagraphStyle("cellb", fontName=BOLD_FONT, fontSize=9.5, leading=12, textColor=colors.white),
    "code": ParagraphStyle("code", fontName="Courier", fontSize=7.6, leading=9.4, leftIndent=6, backColor=colors.HexColor("#f5f7f6"), borderPadding=6),
    "center": ParagraphStyle("center", fontName=BODY_FONT, fontSize=12, leading=18, alignment=TA_CENTER),
    "ref": ParagraphStyle("ref", fontName=BODY_FONT, fontSize=11.5, leading=16, leftIndent=22, firstLineIndent=-22, spaceAfter=6, alignment=TA_LEFT),
    "toc1": ParagraphStyle("toc1", fontName=BOLD_FONT, fontSize=11.5, leading=17, leftIndent=0),
    "toc2": ParagraphStyle("toc2", fontName=BODY_FONT, fontSize=11, leading=15, leftIndent=18),
}

FIGURES, TABLES = [], []


class ReportDoc(BaseDocTemplate):
    """Roman page numbers for the preliminaries, Arabic from Chapter One; builds the TOC."""

    def __init__(self, filename, **kw):
        super().__init__(filename, pagesize=A4, leftMargin=1.25 * inch, rightMargin=1 * inch,
                         topMargin=1 * inch, bottomMargin=1 * inch, **kw)
        frame = Frame(self.leftMargin, self.bottomMargin, self.width, self.height, id="f")
        self.addPageTemplates([
            PageTemplate(id="cover", frames=[frame], onPage=lambda c, d: None),
            PageTemplate(id="front", frames=[frame], onPage=self._roman),
            PageTemplate(id="main", frames=[frame], onPage=self._arabic),
        ])
        self.main_start = None

    def beforeDocument(self):
        # multiBuild runs several passes; the front matter grows once the TOC is filled in.
        self.main_start = None

    def handle_pageBegin(self):
        super().handle_pageBegin()
        if self.pageTemplate.id == "main" and self.main_start is None:
            self.main_start = self.page

    def _roman(self, canv, doc):
        self._footer(canv, to_roman(doc.page))

    def _arabic(self, canv, doc):
        self._footer(canv, str(doc.page - (doc.main_start or doc.page) + 1))

    def _footer(self, canv, label):
        canv.saveState()
        canv.setFont(BODY_FONT, 10)
        canv.setFillColor(GREY)
        canv.drawCentredString(A4[0] / 2 + 0.125 * inch, 0.6 * inch, label)
        canv.restoreState()

    def displayed_page(self):
        """Arabic page numbers are positive; preliminary (roman) pages are encoded as negatives."""
        if self.pageTemplate.id == "main" and self.main_start is not None:
            return self.page - self.main_start + 1
        return -self.page

    def afterFlowable(self, flowable):
        key = getattr(flowable, "_toc", None)
        if key:
            level, text = key
            self.notify("TOCEntry", (level, text, self.displayed_page()))
        for attr, kind in (("_lof", "FigEntry"), ("_lot", "TabEntry")):
            entry = getattr(flowable, attr, None)
            if entry:
                self.notify(kind, (0, entry, self.displayed_page()))


class ListOf(TableOfContents):
    """A table-of-contents style list fed by FigEntry / TabEntry notifications."""

    def __init__(self, kind, **kw):
        super().__init__(**kw)
        self.kind = kind

    def notify(self, kind, stuff):
        if kind == self.kind:
            self.addEntry(*stuff)


def page_label(n):
    n = int(n)
    return to_roman(-n) if n < 0 else str(n)


def to_roman(n):
    vals = [(10, "x"), (9, "ix"), (5, "v"), (4, "iv"), (1, "i")]
    out = ""
    for v, s in vals:
        while n >= v:
            out += s
            n -= v
    return out or "i"


# --------------------------------------------------------------------------- helpers
story = []


def P(text, style="body"):
    story.append(Paragraph(text, styles[style]))


def bullets(items, style="bullet"):
    for it in items:
        story.append(Paragraph(it, styles[style], bulletText="\u2022"))
    story.append(Spacer(1, 4))


def numbered(items):
    for i, it in enumerate(items, 1):
        story.append(Paragraph(it, styles["bullet"], bulletText=f"{i}."))
    story.append(Spacer(1, 4))


def toc_para(text, style, level, toc_text=None):
    p = Paragraph(text, styles[style])
    p._toc = (level, toc_text or text)
    return p


def chapter(number_word, title):
    story.append(NextPageTemplate("main"))
    story.append(PageBreak())
    story.append(Paragraph(f"CHAPTER {number_word}", styles["chapter"]))
    story.append(toc_para(title, "chaptertitle", 0, f"CHAPTER {number_word}: {title}"))


def front_heading(title, toc=True):
    story.append(PageBreak())
    story.append(toc_para(title, "front", 0) if toc else Paragraph(title, styles["front"]))


def h2(text):
    story.append(CondPageBreak(1.3 * inch))
    story.append(toc_para(text, "h2", 1))


def h3(text):
    story.append(CondPageBreak(1 * inch))
    P(text, "h3")


def table(caption, header, rows, widths, font_size=9.5):
    TABLES.append(caption)
    cell = ParagraphStyle("c", parent=styles["cell"], fontSize=font_size, leading=font_size + 2.5)
    head = ParagraphStyle("h", parent=styles["cellb"], fontSize=font_size, leading=font_size + 2.5)
    data = [[Paragraph(h, head) for h in header]] + [[Paragraph(str(c), cell) for c in r] for r in rows]
    t = Table(data, colWidths=widths, repeatRows=1)
    t.setStyle(TableStyle([
        ("BACKGROUND", (0, 0), (-1, 0), GREEN),
        ("ROWBACKGROUNDS", (0, 1), (-1, -1), [colors.white, LIGHT]),
        ("GRID", (0, 0), (-1, -1), 0.4, colors.HexColor("#b9c7bf")),
        ("VALIGN", (0, 0), (-1, -1), "TOP"),
        ("LEFTPADDING", (0, 0), (-1, -1), 4), ("RIGHTPADDING", (0, 0), (-1, -1), 4),
        ("TOPPADDING", (0, 0), (-1, -1), 3), ("BOTTOMPADDING", (0, 0), (-1, -1), 3),
    ]))
    cap = Paragraph(f"<b>Table {caption}</b>", styles["caption"])
    cap._lot = f"Table {caption}"
    story.append(cap)
    story.append(t)
    story.append(Spacer(1, 12))


def figure(flowable, caption):
    FIGURES.append(caption)
    cap = Paragraph(f"Figure {caption}", styles["caption"])
    cap._lof = f"Figure {caption}"
    story.append(KeepTogether([flowable, cap]))


def screenshot(path, caption, width=6.1 * inch, crop=None):
    img = PILImage.open(path).convert("RGB")
    if crop:
        img = img.crop(crop)
    buf = BytesIO()
    img.save(buf, "JPEG", quality=82, optimize=True)
    buf.seek(0)
    w, h = img.size
    height = width * h / w
    max_h = 7.4 * inch
    if height > max_h:
        width, height = width * max_h / height, max_h
    im = Image(buf, width=width, height=height)
    im.hAlign = "CENTER"
    frame = Table([[im]], style=[("BOX", (0, 0), (-1, -1), 0.6, colors.HexColor("#9aa8a0")), ("LEFTPADDING", (0, 0), (-1, -1), 0), ("RIGHTPADDING", (0, 0), (-1, -1), 0), ("TOPPADDING", (0, 0), (-1, -1), 0), ("BOTTOMPADDING", (0, 0), (-1, -1), 0)])
    figure(frame, caption)


def wrap_code(lines, width=92):
    """Soft-wrap long source lines so nothing runs past the page margin."""
    out = []
    for line in lines:
        indent = len(line) - len(line.lstrip())
        while len(line) > width:
            cut = line.rfind(" ", indent + 20, width)
            cut = cut if cut > 0 else width
            out.append(line[:cut])
            line = " " * (indent + 4) + line[cut:].lstrip()
        out.append(line)
    return out


def code(path, start=None, end=None, title=None):
    with open(os.path.join(PROJECT, path), encoding="utf-8") as f:
        lines = f.read().splitlines()
    lines = lines[(start or 1) - 1:end]
    if title:
        P(f"<b>{title}</b> <font size=10 color='#555555'>({path})</font>", "bodyleft")
    story.append(Preformatted("\n".join(wrap_code(lines)), styles["code"]))
    story.append(Spacer(1, 10))


# --------------------------------------------------------------------------- diagrams
def arrow(d, x1, y1, x2, y2, col=colors.HexColor("#333333"), head=6):
    import math
    d.add(Line(x1, y1, x2, y2, strokeColor=col, strokeWidth=1))
    ang = math.atan2(y2 - y1, x2 - x1)
    p1 = (x2 - head * math.cos(ang - 0.4), y2 - head * math.sin(ang - 0.4))
    p2 = (x2 - head * math.cos(ang + 0.4), y2 - head * math.sin(ang + 0.4))
    d.add(Polygon([x2, y2, p1[0], p1[1], p2[0], p2[1]], fillColor=col, strokeColor=col))


def box(d, x, y, w, h, title, lines=(), fill=LIGHT, title_fill=GREEN, fs=7.2):
    d.add(Rect(x, y, w, h, fillColor=fill, strokeColor=GREEN, strokeWidth=0.8))
    d.add(Rect(x, y + h - 13, w, 13, fillColor=title_fill, strokeColor=GREEN, strokeWidth=0.8))
    d.add(String(x + w / 2, y + h - 9.5, title, fontName=BOLD_FONT, fontSize=8, fillColor=colors.white, textAnchor="middle"))
    for i, ln in enumerate(lines):
        d.add(String(x + 4, y + h - 23 - i * 9, ln, fontName="Helvetica", fontSize=fs, fillColor=colors.black))


def label(d, x, y, text, size=8, bold=False, anchor="middle", col=colors.black):
    d.add(String(x, y, text, fontName=BOLD_FONT if bold else "Helvetica", fontSize=size, fillColor=col, textAnchor=anchor))


def architecture_diagram():
    d = Drawing(440, 300)
    # Client
    box(d, 10, 225, 120, 60, "Client (Browser)", ["Public website (HTML/CSS/JS)", "Admin panel (Tailwind,", "Alpine.js, Trix editor)"], fs=7)
    # Laravel
    d.add(Rect(160, 20, 270, 270, fillColor=colors.HexColor("#f8fbf9"), strokeColor=GOLD, strokeWidth=1, strokeDashArray=[4, 3]))
    label(d, 295, 278, "Laravel 12 application (PHP 8.2)", 8.5, True, col=GOLD)
    box(d, 175, 215, 110, 50, "Routes + Middleware", ["web.php, auth, admin,", "CSRF, throttle"], fs=7)
    box(d, 305, 215, 110, 50, "Form Requests", ["Validation rules,", "authorisation"], fs=7)
    box(d, 175, 145, 110, 55, "Controllers", ["Public (Home, Fixture...)", "Admin (Team, Player...)"], fs=7)
    box(d, 305, 145, 110, 55, "Services", ["LeagueTableService", "PlayerStatisticsService", "ImageOptimizer, Importer"], fs=7)
    box(d, 175, 75, 110, 55, "Eloquent Models", ["Team, Player, Fixture,", "MatchEvent, NewsPost..."], fs=7)
    box(d, 305, 75, 110, 55, "Blade Views", ["layouts, components,", "pages/*, admin/*"], fs=7)
    box(d, 175, 28, 240, 32, "Storage (public disk)", ["Uploaded logos, photos (WebP)"], fs=7)
    # DB
    box(d, 10, 75, 120, 55, "MariaDB / MySQL", ["17 migrations", "relational tables,", "foreign keys"], fs=7)
    arrow(d, 130, 250, 175, 245)
    arrow(d, 175, 235, 130, 240)
    label(d, 152, 256, "HTTP", 7)
    arrow(d, 230, 215, 230, 200)
    arrow(d, 285, 240, 305, 240)
    arrow(d, 285, 172, 305, 172)
    arrow(d, 230, 145, 230, 130)
    arrow(d, 285, 100, 305, 100)
    arrow(d, 175, 102, 130, 102)
    label(d, 152, 108, "SQL", 7)
    arrow(d, 360, 145, 360, 130)
    return d


def use_case_diagram():
    d = Drawing(440, 330)

    def actor(x, y, name):
        d.add(Circle(x, y + 36, 7, fillColor=colors.white, strokeColor=colors.black))
        d.add(Line(x, y + 29, x, y + 12))
        d.add(Line(x - 10, y + 23, x + 10, y + 23))
        d.add(Line(x, y + 12, x - 8, y))
        d.add(Line(x, y + 12, x + 8, y))
        label(d, x, y - 10, name, 8, True)

    d.add(Rect(95, 5, 250, 320, fillColor=None, strokeColor=GREEN, strokeWidth=1))
    label(d, 220, 313, "BOUESTI Sports Portal", 9, True, col=GREEN)
    visitor = ["Explore sports and facilities", "View fixtures and results", "View league table", "Browse teams, squads, players", "Read news, gallery, videos", "Search the site"]
    student = ["Apply for trials", "Send contact message"]
    admin = ["Manage seasons and competitions", "Manage teams and players", "Schedule fixtures, enter results", "Record events and line-ups", "Publish news, gallery, videos", "Review applications and messages"]
    ys = [294, 272, 250, 228, 206, 184]
    for i, t in enumerate(visitor):
        d.add(Ellipse(220, ys[i], 88, 10, fillColor=LIGHT, strokeColor=GREEN))
        label(d, 220, ys[i] - 3, t, 7)
    for i, t in enumerate(student):
        y = 162 - i * 24
        d.add(Ellipse(220, y, 88, 10, fillColor=colors.HexColor("#fdf6e3"), strokeColor=GOLD))
        label(d, 220, y - 3, t, 7)
    for i, t in enumerate(admin):
        y = 104 - i * 18
        d.add(Ellipse(220, y, 95, 8.5, fillColor=colors.HexColor("#e8eef8"), strokeColor=colors.HexColor("#1e3a8a")))
        label(d, 220, y - 2.6, t, 6.8)
    actor(45, 225, "Visitor")
    actor(45, 130, "Student")
    actor(395, 55, "Administrator")
    for y in ys:
        d.add(Line(58, 250, 132, y, strokeColor=colors.HexColor("#888888"), strokeWidth=0.5))
    for i in range(2):
        d.add(Line(58, 155, 132, 162 - i * 24, strokeColor=colors.HexColor("#888888"), strokeWidth=0.5))
    for i in range(6):
        d.add(Line(382, 80, 315, 104 - i * 18, strokeColor=colors.HexColor("#888888"), strokeWidth=0.5))
    label(d, 45, 108, "(also a Visitor)", 6.5)
    return d


def context_dfd():
    d = Drawing(440, 230)
    d.add(Circle(220, 115, 58, fillColor=LIGHT, strokeColor=GREEN, strokeWidth=1.2))
    label(d, 220, 120, "0", 9, True, col=GREEN)
    label(d, 220, 108, "BOUESTI Sports", 8.5, True)
    label(d, 220, 97, "Portal", 8.5, True)
    for x, y, t in [(20, 170, "Visitor / Student"), (320, 170, "Administrator"), (320, 20, "Club email (SMTP)")]:
        d.add(Rect(x, y, 100, 36, fillColor=colors.white, strokeColor=colors.black))
        label(d, x + 50, y + 15, t, 8, True)
    arrow(d, 120, 188, 165, 150); label(d, 108, 140, "requests, trial application,", 6.8); label(d, 108, 131, "contact message", 6.8)
    arrow(d, 175, 160, 120, 180); label(d, 170, 205, "pages: fixtures, results,", 6.8); label(d, 170, 196, "table, players, news", 6.8)
    arrow(d, 320, 185, 272, 150); label(d, 345, 145, "teams, players, fixtures,", 6.8, anchor="middle"); label(d, 345, 136, "results, events, content", 6.8)
    arrow(d, 268, 160, 320, 196); label(d, 280, 212, "dashboard, statistics, inbox", 6.8)
    arrow(d, 268, 80, 320, 45); label(d, 290, 44, "message notification", 6.8, anchor="end")
    return d


def erd_diagram():
    d = Drawing(450, 520)
    W, H = 120, 0
    ents = {
        "Season": (10, 440, ["PK id", "name, slug", "is_current"]),
        "Competition": (10, 330, ["PK id", "FK season_id", "name, type"]),
        "Fixture": (165, 330, ["PK id", "FK competition_id", "FK season_id", "FK home/away_team_id", "status, scores"]),
        "Team": (320, 440, ["PK id", "name, slug", "colours, logo"]),
        "Player": (320, 300, ["PK id", "FK team_id", "names, position", "jersey_number"]),
        "MatchEvent": (165, 190, ["PK id", "FK fixture_id, team_id", "FK player_id", "FK related_player_id", "type, minute"]),
        "FixturePlayer": (320, 175, ["PK id", "FK fixture_id", "FK player_id, team_id", "is_starting"]),
        "NewsPost": (10, 190, ["PK id", "FK author_id", "FK fixture_id", "title, content"]),
        "User": (10, 70, ["PK id", "name, email", "role"]),
        "GalleryItem": (165, 70, ["PK id", "FK fixture_id", "FK team_id", "image"]),
        "Staff": (320, 70, ["PK id", "FK team_id", "name, role, type"]),
    }
    geo = {}
    for name, (x, y, lines) in ents.items():
        h = 16 + len(lines) * 9 + 4
        box(d, x, y, W, h, name, lines)
        geo[name] = (x, y, W, h)

    def mid(n, side):
        x, y, w, h = geo[n]
        return {"top": (x + w / 2, y + h), "bottom": (x + w / 2, y), "left": (x, y + h / 2), "right": (x + w, y + h / 2)}[side]

    def rel(a, sa, b, sb, t1="1", t2="N"):
        (x1, y1), (x2, y2) = mid(a, sa), mid(b, sb)
        d.add(Line(x1, y1, x2, y2, strokeColor=colors.HexColor("#444444"), strokeWidth=0.8))
        label(d, x1 + (x2 - x1) * 0.12, y1 + (y2 - y1) * 0.12 + 3, t1, 7, True, col=GOLD)
        label(d, x1 + (x2 - x1) * 0.88, y1 + (y2 - y1) * 0.88 + 3, t2, 7, True, col=GOLD)

    rel("Season", "bottom", "Competition", "top")
    rel("Competition", "right", "Fixture", "left")
    rel("Team", "bottom", "Player", "top")
    rel("Team", "left", "Fixture", "top", "1", "")
    label(d, 190, 399, "N (home / away)", 7, True, col=GOLD)
    rel("Fixture", "bottom", "MatchEvent", "top")
    rel("Player", "left", "MatchEvent", "right")
    rel("Fixture", "right", "FixturePlayer", "top")
    rel("Player", "bottom", "FixturePlayer", "top")
    rel("User", "top", "NewsPost", "bottom")
    rel("Fixture", "left", "NewsPost", "top", "1", "0..N")
    # Team 1..N Staff: routed around the right-hand boxes.
    (tx, ty), (sx, sy) = mid("Team", "right"), mid("Staff", "right")
    for (x1, y1, x2, y2) in [(tx, ty, 447, ty), (447, ty, 447, sy), (447, sy, sx, sy)]:
        d.add(Line(x1, y1, x2, y2, strokeColor=colors.HexColor("#444444"), strokeWidth=0.8))
    label(d, 437, ty + 3, "1", 7, True, col=GOLD)
    label(d, 434, sy + 3, "N", 7, True, col=GOLD)
    label(d, 225, 12, "GalleryItem optionally references Fixture and Team. Stand-alone: videos, trial_applications, contact_messages.", 7)
    return d


def league_flowchart():
    d = Drawing(440, 360)
    steps = [
        ("Start: competition selected", 320),
        ("Load all fixtures of the competition", 282),
        ("Create an empty row for every team in those fixtures", 244),
        ("For each COMPLETED fixture (in date order)", 206),
        ("Update both teams: P, GF, GA; W/D/L; points (3/1/0); form", 168),
        ("Goal difference = GF - GA; keep last five results", 130),
        ("Sort: points, goal difference, goals for, name", 92),
        ("Assign positions 1..n and return the table", 54),
    ]
    for text, y in steps:
        d.add(Rect(95, y, 250, 26, fillColor=LIGHT if "For each" not in text else colors.HexColor("#fdf6e3"), strokeColor=GREEN, rx=6, ry=6))
        label(d, 220, y + 9.5, text, 7.8)
    for (_, y1), (_, y2) in zip(steps, steps[1:]):
        arrow(d, 220, y1, 220, y2 + 26)
    d.add(Line(345, 181, 385, 181)); d.add(Line(385, 181, 385, 219)); arrow(d, 385, 219, 345, 219)
    label(d, 395, 198, "next", 7, anchor="start")
    d.add(Rect(170, 12, 100, 24, fillColor=GREEN, strokeColor=GREEN, rx=12, ry=12))
    label(d, 220, 20.5, "End", 8, True, col=colors.white)
    arrow(d, 220, 54, 220, 36)
    return d


# --------------------------------------------------------------------------- build
C.build(globals())

doc = ReportDoc(OUT, title=getattr(C, "PDF_TITLE", "BOUESTI Sports Portal — Project Documentation"), author=C.STUDENT_NAME,
                subject="Final year project report")
os.makedirs(os.path.dirname(OUT), exist_ok=True)
doc.multiBuild(story)
print("written", OUT)
