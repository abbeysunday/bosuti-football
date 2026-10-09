// Renders docx_build/blocks.json (written by export_blocks.py) into a Word document.
// Usage: python export_blocks.py sem && node build_docx.cjs
// Needs the `docx` npm package (npm install docx --prefix <dir> and set NODE_PATH, or install here).
const fs = require("fs");
const path = require("path");
const {
  Document, Packer, Paragraph, TextRun, ImageRun, Table, TableRow, TableCell, WidthType, ShadingType, BorderStyle,
  AlignmentType, HeadingLevel, PageBreak, Footer, PageNumber, NumberFormat, LevelFormat, TableOfContents, StyleLevel,
  VerticalAlign, TableLayoutType,
} = require("docx");

const { meta, blocks } = JSON.parse(fs.readFileSync(path.join(__dirname, "docx_build", "blocks.json"), "utf8"));

const GREEN = "0A5C36", LIGHT = "EEF4F0", GRID = "B9C7BF", GREY = "555555";
const FONT = "Times New Roman";
const DXA = (inches) => Math.round(inches * 1440);
const CONTENT_WIDTH = DXA(6.0);

const runs = (rs, extra = {}) => rs.map((r) => r.break
  ? new TextRun({ break: 1 })
  : new TextRun({ text: r.text, bold: r.bold || extra.bold, italics: r.italic || extra.italics,
      size: r.small ? 20 : extra.size, color: r.small ? GREY : extra.color, font: extra.font }));

const ALIGN = { body: AlignmentType.JUSTIFIED, bodyleft: AlignmentType.LEFT, center: AlignmentType.CENTER, ref: AlignmentType.LEFT };

// ------------------------------------------------------------------ list instances (each numbered list restarts)
let listInstance = 0;

function para(b) {
  const style = b.style || "body";
  if (style === "ref") {
    return new Paragraph({ style: "Reference", children: runs(b.runs) });
  }
  return new Paragraph({ alignment: ALIGN[style] || AlignmentType.JUSTIFIED, children: runs(b.runs) });
}

function captionPara(text, styleId) {
  return new Paragraph({ style: styleId, children: [new TextRun(text)] });
}

function cellBorders(color) {
  const b = { style: BorderStyle.SINGLE, size: 4, color };
  return { top: b, bottom: b, left: b, right: b };
}

function dataTable(b) {
  const total = b.widths.reduce((a, w) => a + w, 0);
  const widths = b.widths.map((w) => Math.round((w / total) * Math.min(CONTENT_WIDTH, DXA(total))));
  const tableWidth = widths.reduce((a, w) => a + w, 0);
  const size = Math.round(b.font * 2);
  const cell = (rs, i, header, shade) => new TableCell({
    width: { size: widths[i], type: WidthType.DXA },
    borders: cellBorders(GRID),
    shading: header ? { fill: GREEN, type: ShadingType.CLEAR, color: "auto" } : (shade ? { fill: LIGHT, type: ShadingType.CLEAR, color: "auto" } : undefined),
    margins: { top: 40, bottom: 40, left: 80, right: 80 },
    children: [new Paragraph({ spacing: { line: 240, before: 0, after: 0 }, alignment: AlignmentType.LEFT,
      children: runs(rs, header ? { bold: true, color: "FFFFFF", size } : { size }) })],
  });
  const rows = [new TableRow({ tableHeader: true, cantSplit: true, children: b.header.map((h, i) => cell(h, i, true)) })];
  b.rows.forEach((r, ri) => rows.push(new TableRow({ cantSplit: true, children: r.map((c, i) => cell(c, i, false, ri % 2 === 1)) })));
  return new Table({ width: { size: tableWidth, type: WidthType.DXA }, columnWidths: widths, layout: TableLayoutType.FIXED, rows });
}

function plainTable(b) {
  const widths = b.widths.map(DXA);
  const none = { style: BorderStyle.NONE, size: 0, color: "FFFFFF" };
  return new Table({
    width: { size: widths.reduce((a, w) => a + w, 0), type: WidthType.DXA }, columnWidths: widths,
    borders: { top: none, bottom: none, left: none, right: none, insideHorizontal: none, insideVertical: none },
    rows: b.rows.map((r) => new TableRow({ children: r.map((c, i) => new TableCell({
      width: { size: widths[i], type: WidthType.DXA }, borders: { top: none, bottom: none, left: none, right: none },
      children: [new Paragraph({ spacing: { line: 276, before: 0, after: 0 }, children: [new TextRun({ text: c, size: 23 })] })],
    })) })),
  });
}

function image(b) {
  const data = fs.readFileSync(b.path);
  const type = /\.jpe?g$/i.test(b.path) ? "jpg" : "png";
  const w = Math.round(b.width * 96);
  const h = Math.round(w * b.ratio);
  return new Paragraph({ alignment: AlignmentType.CENTER, keepNext: !!b.caption, spacing: { before: 120, after: 0, line: 240 },
    children: [new ImageRun({ type, data, transformation: { width: w, height: h },
      altText: { title: b.caption || "Image", description: b.caption || "Image", name: path.basename(b.path) } })] });
}

function codeBlock(b) {
  return b.lines.map((line, i) => new Paragraph({
    style: "Code", spacing: { before: i === 0 ? 60 : 0, after: i === b.lines.length - 1 ? 200 : 0, line: 240 },
    children: [new TextRun({ text: line.replace(/\t/g, "    ") || " " })],
  }));
}

// ------------------------------------------------------------------ sections
const footer = (fmt) => new Footer({ children: [new Paragraph({ alignment: AlignmentType.CENTER,
  children: [new TextRun({ children: [PageNumber.CURRENT], size: 20, color: GREY })] })] });

const sections = [];
let current = null;
let pageBreakPending = false;
let sectionJustStarted = false;

function startSection(kind) {
  const page = { size: { width: 11906, height: 16838 }, margin: { top: 1440, bottom: 1440, left: 1800, right: 1440, footer: 700 } };
  const props = { page };
  let footers;
  if (kind === "front") { props.page.pageNumbers = { start: 2, formatType: NumberFormat.LOWER_ROMAN }; footers = { default: footer() }; }
  if (kind === "main") { props.page.pageNumbers = { start: 1, formatType: NumberFormat.DECIMAL }; footers = { default: footer() }; }
  current = { kind, properties: props, footers, children: [] };
  sections.push(current);
  sectionJustStarted = true;
  pageBreakPending = false;
}

function push(...items) {
  if (pageBreakPending && !sectionJustStarted) {
    current.children.push(new Paragraph({ children: [new PageBreak()] }));
  }
  pageBreakPending = false;
  sectionJustStarted = false;
  current.children.push(...items);
}

for (const b of blocks) {
  switch (b.t) {
    case "section":
      if (!current || current.kind !== b.kind) startSection(b.kind);
      else pageBreakPending = true;
      break;
    case "pagebreak": pageBreakPending = true; break;
    case "space":
      push(new Paragraph({ spacing: { before: 0, after: Math.round(b.pt * 20), line: 240 }, children: [] }));
      break;
    case "p": push(para(b)); break;
    case "li":
      push(new Paragraph({ alignment: AlignmentType.JUSTIFIED, spacing: { after: 60 },
        numbering: b.kind === "number" ? { reference: "numbers", level: 0, instance: listInstance } : { reference: "bullets", level: 0 },
        children: runs(b.runs) }));
      break;
    case "restart": listInstance += 1; break;
    case "chapter":
      push(new Paragraph({ heading: HeadingLevel.HEADING_1, children: [new TextRun(`CHAPTER ${b.number}`), new TextRun({ break: 1 }), new TextRun(b.title)] }));
      break;
    case "front":
      push(b.toc === false
        ? new Paragraph({ style: "FrontTitle", children: [new TextRun(b.title)] })
        : new Paragraph({ heading: HeadingLevel.HEADING_1, children: [new TextRun(b.title)] }));
      break;
    case "h2": push(new Paragraph({ heading: HeadingLevel.HEADING_2, children: [new TextRun(b.text)] })); break;
    case "h3": push(new Paragraph({ heading: HeadingLevel.HEADING_3, children: [new TextRun(b.text)] })); break;
    case "table":
      push(captionPara(b.caption, "TableCaption"), dataTable(b), new Paragraph({ spacing: { after: 120, line: 240 }, children: [] }));
      break;
    case "plaintable": push(plainTable(b)); break;
    case "image":
      push(image(b));
      if (b.caption) push(captionPara(b.caption, "FigureCaption"));
      break;
    case "code": push(...codeBlock(b)); break;
    case "toc":
      push(new TableOfContents("Table of Contents", { hyperlink: true, headingStyleRange: "1-2" }));
      break;
    case "lot":
      push(new TableOfContents("List of Tables", { hyperlink: true, stylesWithLevels: [new StyleLevel("Table Caption", 1)] }));
      break;
    case "lof":
      push(new TableOfContents("List of Figures", { hyperlink: true, stylesWithLevels: [new StyleLevel("Figure Caption", 1)] }));
      break;
    default: throw new Error("unknown block " + b.t);
  }
}

// ------------------------------------------------------------------ document
const doc = new Document({
  creator: meta.author, title: meta.title,
  features: { updateFields: true },
  styles: {
    default: { document: { run: { font: FONT, size: 24 }, paragraph: { spacing: { line: 360, after: 120 } } } },
    paragraphStyles: [
      { id: "Heading1", name: "Heading 1", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { font: FONT, size: 28, bold: true, color: "000000" },
        paragraph: { alignment: AlignmentType.CENTER, spacing: { before: 0, after: 360, line: 360 }, outlineLevel: 0 } },
      { id: "Heading2", name: "Heading 2", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { font: FONT, size: 25, bold: true, color: GREEN },
        paragraph: { spacing: { before: 240, after: 120, line: 360 }, keepNext: true, keepLines: true, outlineLevel: 1 } },
      { id: "Heading3", name: "Heading 3", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { font: FONT, size: 24, bold: true },
        paragraph: { spacing: { before: 160, after: 80, line: 360 }, keepNext: true, outlineLevel: 2 } },
      { id: "FrontTitle", name: "Front Title", basedOn: "Normal", next: "Normal",
        run: { font: FONT, size: 28, bold: true }, paragraph: { alignment: AlignmentType.CENTER, spacing: { after: 360 } } },
      { id: "TableCaption", name: "Table Caption", basedOn: "Normal", next: "Normal",
        run: { font: FONT, size: 21, bold: true, italics: true, color: "333333" },
        paragraph: { alignment: AlignmentType.CENTER, keepNext: true, spacing: { before: 120, after: 80, line: 276 } } },
      { id: "FigureCaption", name: "Figure Caption", basedOn: "Normal", next: "Normal",
        run: { font: FONT, size: 21, italics: true, color: "333333" },
        paragraph: { alignment: AlignmentType.CENTER, spacing: { before: 60, after: 240, line: 276 } } },
      { id: "Reference", name: "Reference", basedOn: "Normal", next: "Reference",
        run: { font: FONT, size: 23 }, paragraph: { indent: { left: 440, hanging: 440 }, spacing: { after: 120, line: 300 } } },
      { id: "Code", name: "Code", basedOn: "Normal", next: "Code",
        run: { font: "Courier New", size: 15 },
        paragraph: { shading: { fill: "F5F7F6", type: ShadingType.CLEAR, color: "auto" }, indent: { left: 120 } } },
    ],
  },
  numbering: {
    config: [
      { reference: "bullets", levels: [{ level: 0, format: LevelFormat.BULLET, text: "•", alignment: AlignmentType.LEFT,
        style: { paragraph: { indent: { left: 540, hanging: 300 } } } }] },
      { reference: "numbers", levels: [{ level: 0, format: LevelFormat.DECIMAL, text: "%1.", alignment: AlignmentType.LEFT,
        style: { paragraph: { indent: { left: 540, hanging: 360 } } } }] },
    ],
  },
  sections: sections.map((s) => ({ properties: s.properties, footers: s.footers, children: s.children })),
});

Packer.toBuffer(doc).then((buf) => {
  fs.writeFileSync(meta.out, buf);
  console.log("written", meta.out);
});
