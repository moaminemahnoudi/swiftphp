<?php

namespace SwiftPHP\Export;

use SwiftPHP\Support\Collection;

/**
 * SwiftPHP Export System
 * 
 * Export data to multiple formats: Excel (XLSX), PDF, CSV, JSON
 * Usage: Exporter::make($data)->excel('users.xlsx')
 */
class Exporter
{
    protected array $data = [];
    protected array $headers = [];
    protected string $title = '';
    protected array $styles = [];

    public function __construct(array|Collection $data)
    {
        $this->data = $data instanceof Collection ? $data->toArray() : $data;
    }

    public static function make(array|Collection $data): self
    {
        return new self($data);
    }

    /**
     * Set custom headers
     */
    public function headers(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Set document title
     */
    public function title(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Export to Excel (XLSX)
     */
    public function excel(string $filename = 'export.xlsx'): string
    {
        $xml = $this->generateExcelXML();
        
        // Create temporary file
        $tmpFile = sys_get_temp_dir() . '/' . uniqid() . '.xlsx';
        
        // Simple XLSX structure (ZIP with XML)
        $zip = new \ZipArchive();
        if ($zip->open($tmpFile, \ZipArchive::CREATE) === true) {
            // Add worksheet
            $zip->addFromString('xl/worksheets/sheet1.xml', $xml['sheet']);
            $zip->addFromString('xl/workbook.xml', $xml['workbook']);
            $zip->addFromString('[Content_Types].xml', $xml['contentTypes']);
            $zip->addFromString('_rels/.rels', $xml['rels']);
            $zip->addFromString('xl/_rels/workbook.xml.rels', $xml['workbookRels']);
            $zip->addFromString('xl/styles.xml', $xml['styles']);
            $zip->close();
        }

        // Download file
        $this->downloadFile($tmpFile, $filename);
        return $tmpFile;
    }

    /**
     * Export to CSV
     */
    public function csv(string $filename = 'export.csv', string $delimiter = ',', string $enclosure = '"'): void
    {
        $tmpFile = sys_get_temp_dir() . '/' . uniqid() . '.csv';
        $handle = fopen($tmpFile, 'w');

        // Write headers
        if (!empty($this->headers)) {
            fputcsv($handle, $this->headers, $delimiter, $enclosure);
        } elseif (!empty($this->data)) {
            fputcsv($handle, array_keys((array)$this->data[0]), $delimiter, $enclosure);
        }

        // Write data
        foreach ($this->data as $row) {
            fputcsv($handle, (array)$row, $delimiter, $enclosure);
        }

        fclose($handle);
        $this->downloadFile($tmpFile, $filename);
    }

    /**
     * Export to JSON
     */
    public function json(string $filename = 'export.json', bool $pretty = true): void
    {
        $options = $pretty ? JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE : 0;
        $json = json_encode($this->data, $options);

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $json;
        exit;
    }

    /**
     * Export to PDF
     */
    public function pdf(string $filename = 'export.pdf'): void
    {
        $html = $this->generatePDFHTML();
        $pdf = $this->htmlToPDF($html);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $pdf;
        exit;
    }

    /**
     * Export to XML
     */
    public function xml(string $filename = 'export.xml', string $rootElement = 'data', string $rowElement = 'row'): void
    {
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><{$rootElement}></{$rootElement}>");

        foreach ($this->data as $row) {
            $rowNode = $xml->addChild($rowElement);
            foreach ((array)$row as $key => $value) {
                $rowNode->addChild($key, htmlspecialchars((string)$value));
            }
        }

        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $xml->asXML();
        exit;
    }

    /**
     * Generate Excel XML
     */
    protected function generateExcelXML(): array
    {
        $headers = $this->headers ?: (!empty($this->data) ? array_keys((array)$this->data[0]) : []);
        
        // Generate worksheet
        $rows = '';
        $rowNum = 1;

        // Header row
        if (!empty($headers)) {
            $cells = '';
            $colNum = 0;
            foreach ($headers as $header) {
                $cellRef = $this->columnLetter($colNum++) . $rowNum;
                $cells .= "<c r=\"{$cellRef}\" t=\"inlineStr\" s=\"1\"><is><t>" . htmlspecialchars($header) . "</t></is></c>";
            }
            $rows .= "<row r=\"{$rowNum}\">{$cells}</row>";
            $rowNum++;
        }

        // Data rows
        foreach ($this->data as $row) {
            $cells = '';
            $colNum = 0;
            foreach ((array)$row as $value) {
                $cellRef = $this->columnLetter($colNum++) . $rowNum;
                if (is_numeric($value)) {
                    $cells .= "<c r=\"{$cellRef}\"><v>{$value}</v></c>";
                } else {
                    $cells .= "<c r=\"{$cellRef}\" t=\"inlineStr\"><is><t>" . htmlspecialchars($value) . "</t></is></c>";
                }
            }
            $rows .= "<row r=\"{$rowNum}\">{$cells}</row>";
            $rowNum++;
        }

        return [
            'sheet' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <sheetData>' . $rows . '</sheetData>
</worksheet>',
            'workbook' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <sheets><sheet name="Sheet1" sheetId="1" r:id="rId1" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"/></sheets>
</workbook>',
            'contentTypes' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>',
            'rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>',
            'workbookRels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>',
            'styles' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <fonts count="2">
        <font><sz val="11"/><name val="Calibri"/></font>
        <font><b/><sz val="11"/><name val="Calibri"/></font>
    </fonts>
    <fills count="1"><fill><patternFill patternType="none"/></fill></fills>
    <borders count="1"><border/></borders>
    <cellXfs count="2">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
        <xf numFmtId="0" fontId="1" fillId="0" borderId="0"/>
    </cellXfs>
</styleSheet>'
        ];
    }

    /**
     * Generate PDF HTML
     */
    protected function generatePDFHTML(): string
    {
        $headers = $this->headers ?: (!empty($this->data) ? array_keys((array)$this->data[0]) : []);
        
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background: #007bff; color: white; padding: 10px; text-align: left; font-weight: bold; }
            td { padding: 8px; border-bottom: 1px solid #ddd; }
            tr:hover { background: #f5f5f5; }
        </style></head><body>';

        if ($this->title) {
            $html .= '<h1>' . htmlspecialchars($this->title) . '</h1>';
        }

        $html .= '<table><thead><tr>';
        foreach ($headers as $header) {
            $html .= '<th>' . htmlspecialchars($header) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($this->data as $row) {
            $html .= '<tr>';
            foreach ((array)$row as $value) {
                $html .= '<td>' . htmlspecialchars($value) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';
        return $html;
    }

    /**
     * Convert HTML to PDF (simple implementation)
     */
    protected function htmlToPDF(string $html): string
    {
        // Simple PDF generation using FPDF-style approach
        // For production, consider using libraries like TCPDF or DomPDF
        
        // This is a simplified version - in production use proper PDF library
        $pdf = "%PDF-1.4\n";
        $pdf .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $pdf .= "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $pdf .= "3 0 obj\n<< /Type /Page /Parent 2 0 R /Resources 4 0 R /MediaBox [0 0 612 792] /Contents 5 0 R >>\nendobj\n";
        $pdf .= "4 0 obj\n<< /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> >> >>\nendobj\n";
        
        $content = "BT /F1 12 Tf 50 700 Td (" . strip_tags($html) . ") Tj ET";
        $pdf .= "5 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n{$content}\nendstream\nendobj\n";
        $pdf .= "xref\n0 6\n0000000000 65535 f\n0000000009 00000 n\n0000000058 00000 n\n0000000115 00000 n\n0000000214 00000 n\n0000000303 00000 n\n";
        $pdf .= "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n" . strlen($pdf) . "\n%%EOF";
        
        return $pdf;
    }

    /**
     * Convert column number to letter (0 = A, 1 = B, etc.)
     */
    protected function columnLetter(int $num): string
    {
        $letter = '';
        while ($num >= 0) {
            $letter = chr($num % 26 + 65) . $letter;
            $num = floor($num / 26) - 1;
        }
        return $letter;
    }

    /**
     * Download file
     */
    protected function downloadFile(string $filepath, string $filename): void
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
        $mimeTypes = [
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv' => 'text/csv',
            'pdf' => 'application/pdf',
            'json' => 'application/json',
            'xml' => 'application/xml'
        ];

        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        readfile($filepath);
        unlink($filepath);
        exit;
    }
}
