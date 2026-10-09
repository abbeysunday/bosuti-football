# Project report source

Edit your details at the top of `content.py` (STUDENT_NAME, MATRIC, SUPERVISOR, HOD) and the dedication/acknowledgements text, then run:

    pip install reportlab pillow
    python build_doc.py

The PDF is written to `docs/BOUESTI_Sports_Portal_Project_Documentation.pdf`.

## Sports Event Management report (Okunbonade)

Text: `sem_content.py` (front matter, Chapters 1–2) and `sem_content2.py` (Chapters 3–5, references, appendices).

PDF:

    python build_doc.py sem

Word (.docx) — needs Node.js with the `docx` package (`npm install docx` in a folder outside the Laravel project, then point `NODE_PATH` at its `node_modules`) and Microsoft Word for the last step:

    python export_blocks.py sem
    node build_docx.cjs
    powershell -ExecutionPolicy Bypass -File update_docx_fields.ps1 -Docx ..\Web_Based_Sports_Event_Management_System_Report.docx

The last step fills in the table of contents and the lists of tables and figures. Without it, Word offers to update them when the file is first opened.
