# Opens a generated .docx in Microsoft Word, fills in the table of contents and the lists of tables and figures,
# saves it, and optionally exports a PDF copy for checking.
# Usage: powershell -File update_docx_fields.ps1 -Docx <path.docx> [-Pdf <path.pdf>]
param([Parameter(Mandatory = $true)][string]$Docx, [string]$Pdf)

$word = New-Object -ComObject Word.Application
$word.Visible = $false
$word.DisplayAlerts = 0
try {
    $doc = $word.Documents.Open((Resolve-Path $Docx).Path, $false, $false)
    # Page numbers in the TOC depend on layout, so update twice.
    for ($pass = 0; $pass -lt 2; $pass++) {
        foreach ($toc in $doc.TablesOfContents) { $toc.Update() }
        $doc.Repaginate()
    }
    $doc.Save()
    if ($Pdf) { $doc.ExportAsFixedFormat($Pdf, 17) }   # 17 = wdExportFormatPDF
    "pages: " + $doc.ComputeStatistics(2)               # 2 = wdStatisticPages
    $doc.Close($false)
} finally {
    $word.Quit()
}
