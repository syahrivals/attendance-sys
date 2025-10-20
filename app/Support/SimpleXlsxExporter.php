<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use ZipArchive;

class SimpleXlsxExporter
{
    /**
     * Convert a 2D array to XLSX binary string.
     *
     * @param  array<int, array<int, string|int|float|bool|null>>  $rows
     */
    public static function make(array $rows, string $sheetName = 'Sheet1'): string
    {
        if (empty($rows)) {
            $rows = [[]];
        }

        $tmpFile = self::createTempFile();

        $zip = new ZipArchive();

        if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Tidak dapat membuat file XLSX sementara.');
        }

        $now = CarbonImmutable::now();

        $zip->addFromString('[Content_Types].xml', self::contentTypes());
        $zip->addFromString('_rels/.rels', self::rootRels());
        $zip->addFromString('docProps/app.xml', self::appProperties($sheetName));
        $zip->addFromString('docProps/core.xml', self::coreProperties($now));
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::workbookRels());
        $zip->addFromString('xl/workbook.xml', self::workbook($sheetName));
        $zip->addFromString('xl/styles.xml', self::styles());
        $zip->addFromString('xl/worksheets/sheet1.xml', self::worksheet($rows));

        $zip->close();

        $contents = file_get_contents($tmpFile);

        if ($contents === false) {
            throw new \RuntimeException('Gagal membaca file XLSX sementara.');
        }

        unlink($tmpFile);

        return $contents;
    }

    protected static function createTempFile(): string
    {
        $temp = tempnam(sys_get_temp_dir(), 'xlsx_');

        if ($temp === false) {
            throw new \RuntimeException('Tidak dapat membuat file sementara.');
        }

        return $temp;
    }

    protected static function contentTypes(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
    <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
    <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>
XML;
    }

    protected static function rootRels(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
    <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
XML;
    }

    protected static function appProperties(string $sheetName): string
    {
        $sheetName = htmlspecialchars($sheetName, ENT_QUOTES | ENT_XML1);

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
    <Application>Laravel Export</Application>
    <DocSecurity>0</DocSecurity>
    <ScaleCrop>false</ScaleCrop>
    <HeadingPairs>
        <vt:vector size="2" baseType="variant">
            <vt:variant>
                <vt:lpstr>Worksheets</vt:lpstr>
            </vt:variant>
            <vt:variant>
                <vt:i4>1</vt:i4>
            </vt:variant>
        </vt:vector>
    </HeadingPairs>
    <TitlesOfParts>
        <vt:vector size="1" baseType="lpstr">
            <vt:lpstr>{$sheetName}</vt:lpstr>
        </vt:vector>
    </TitlesOfParts>
    <Company>Attendance System</Company>
</Properties>
XML;
    }

    protected static function coreProperties(CarbonImmutable $timestamp): string
    {
        $created = $timestamp->toIso8601String();

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <dc:creator>Attendance System</dc:creator>
    <cp:lastModifiedBy>Attendance System</cp:lastModifiedBy>
    <dcterms:created xsi:type="dcterms:W3CDTF">{$created}</dcterms:created>
    <dcterms:modified xsi:type="dcterms:W3CDTF">{$created}</dcterms:modified>
</cp:coreProperties>
XML;
    }

    protected static function workbook(string $sheetName): string
    {
        $sheetName = htmlspecialchars($sheetName, ENT_QUOTES | ENT_XML1);

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="{$sheetName}" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>
XML;
    }

    protected static function workbookRels(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>
XML;
    }

    protected static function styles(): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <fonts count="1">
        <font>
            <sz val="11"/>
            <color theme="1"/>
            <name val="Calibri"/>
            <family val="2"/>
            <scheme val="minor"/>
        </font>
    </fonts>
    <fills count="1">
        <fill>
            <patternFill patternType="none"/>
        </fill>
    </fills>
    <borders count="1">
        <border>
            <left/><right/><top/><bottom/><diagonal/>
        </border>
    </borders>
    <cellStyleXfs count="1">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
    </cellStyleXfs>
    <cellXfs count="1">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    </cellXfs>
</styleSheet>
XML;
    }

    protected static function worksheet(array $rows): string
    {
        $rowCount = count($rows);
        $columnCount = max(array_map('count', $rows));
        $dimension = $columnCount > 0 ? 'A1:' . self::cellAddress($columnCount - 1, $rowCount) : 'A1:A1';

        $sheetData = '<sheetData>';

        foreach ($rows as $rowIndex => $row) {
            $sheetData .= '<row r="' . ($rowIndex + 1) . '">';

            foreach (array_values($row) as $colIndex => $value) {
                $cellRef = self::cellAddress($colIndex, $rowIndex + 1);
                $cellValue = htmlspecialchars((string) $value, ENT_QUOTES | ENT_XML1);
                $sheetData .= '<c r="' . $cellRef . '" t="inlineStr"><is><t>' . $cellValue . '</t></is></c>';
            }

            $sheetData .= '</row>';
        }

        $sheetData .= '</sheetData>';

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <dimension ref="{$dimension}"/>
    <sheetViews>
        <sheetView workbookViewId="0"/>
    </sheetViews>
    <sheetFormatPr defaultRowHeight="15"/>
    {$sheetData}
</worksheet>
XML;
    }

    protected static function cellAddress(int $columnIndex, int $rowNumber): string
    {
        $column = '';
        $columnIndex++;

        while ($columnIndex > 0) {
            $modulo = ($columnIndex - 1) % 26;
            $column = chr(65 + $modulo) . $column;
            $columnIndex = (int) (($columnIndex - $modulo) / 26);
        }

        return $column . $rowNumber;
    }
}

