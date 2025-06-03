<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UM_DR_SALES_Export implements FromArray, ShouldAutoSize, WithEvents
{
    protected $file1Rows = [];
    protected $file2Rows = [];

    public function __construct($unmatchedResults)
    {
        foreach ($unmatchedResults as $row) {
            if (isset($row['__source']) && $row['__source'] === 'file1') {
                $this->file1Rows[] = $row;
            } elseif (isset($row['__source']) && $row['__source'] === 'file2') {
                $this->file2Rows[] = $row;
            }
        }
    }

    public function array(): array
    {
        $output = [];

        $output[] = ['DELIVERY ITEMS']; // Title row

        // File 1 Header
        $output[] = [
            'PREPARED BY', 'RECEIVED BY', 'DR#', 'FROM', 'TO', 'DATE', 'CODE', 'NAME', 'UOM', 'COST', 'SRP', 'QTY', 'COST AMOUNT'
        ];

        // File 1 Data
        foreach ($this->file1Rows as $row) {
            $output[] = [
                $row[0] ?? '', $row[1] ?? '', $row[2] ?? '', $row[3] ?? '', $row[4] ?? '',
                $row[5] ?? '', $row[6] ?? '', $row[7] ?? '', $row[8] ?? '', $row[9] ?? '',
                $row[10] ?? '', $row[11] ?? '', $row[12] ?? '', $row[13] ?? '', $row[14] ?? '',
                $row[15] ?? '', $row[16] ?? '',
            ];
        }

        // Empty row between sections (optional)
        $output[] = [''];

        $output[] = ['DELIVERY RECEIVE ITEMS']; // Title row

        // File 2 Header
        $output[] = [
            'PREPARED BY', 'RECEIVED BY', 'DR#', 'REC#', 'FROM', 'TO', 'DATE', 'CATEGORY',
            'SUB CATEGORY', 'CODE', 'NAME', 'UOM', 'COST', 'SRP', 'QTY', 'STATUS', 'COST AMOUNT'
        ];

        // File 2 Data
        foreach ($this->file2Rows as $row) {
            $output[] = [
                $row[0] ?? '', $row[1] ?? '', $row[2] ?? '', $row[3] ?? '', $row[4] ?? '',
                $row[5] ?? '', $row[6] ?? '', $row[7] ?? '', $row[8] ?? '', $row[9] ?? '',
                $row[10] ?? '', $row[11] ?? '', $row[12] ?? '', $row[13] ?? '', $row[14] ?? '',
                $row[15] ?? '', $row[16] ?? '',
            ];
        }

        return $output;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
    
                // === File 1 Title (RECEIVE) ===
                $sheet->mergeCells('A1:M1');
                $sheet->setCellValue('A1', 'DELIVERY ITEMS');
                $sheet->getStyle('A1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('0070C0');
    
                // === File 1 Header ===
                $sheet->getStyle('A2:M2')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                $sheet->getStyle('A2:M2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0070C0');
    
                // === File 2 Title (DELIVERY) ===
                $file1DataCount = count($this->file1Rows);
                $file2TitleRow = $file1DataCount + 4;
                $file2HeaderRow = $file2TitleRow + 1;
                $sheet->mergeCells("A{$file2TitleRow}:Q{$file2TitleRow}");
                $sheet->setCellValue("A{$file2TitleRow}", 'DELIVERY RECEIVE ITEMS');
                $sheet->getStyle("A{$file2TitleRow}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                $sheet->getStyle("A{$file2TitleRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A{$file2TitleRow}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('0070C0');
    
                // === File 2 Header ===
                $sheet->getStyle("A{$file2HeaderRow}:Q{$file2HeaderRow}")
                    ->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
                $sheet->getStyle("A{$file2HeaderRow}:Q{$file2HeaderRow}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0070C0');
    
                    $file1StartRow = 3;
                $file1EndRow = $file1StartRow + $file1DataCount - 1;
                if ($file1EndRow >= $file1StartRow) {
                    $currencyColsFile1 = ['J', 'K', 'M'];
                    foreach ($currencyColsFile1 as $col) {
                        $sheet->getStyle("{$col}{$file1StartRow}:{$col}{$file1EndRow}")
                            ->getNumberFormat()
                            ->setFormatCode('"₱"#,##0.00');
                    }
                }
    
                    $file2StartRow = $file2HeaderRow + 1;
                $file2EndRow = $file2StartRow + count($this->file2Rows) - 1;
                if ($file2EndRow >= $file2StartRow) {
                    $currencyColsFile2 = ['M', 'N', 'Q'];
                    foreach ($currencyColsFile2 as $col) {
                        $sheet->getStyle("{$col}{$file2StartRow}:{$col}{$file2EndRow}")
                            ->getNumberFormat()
                            ->setFormatCode('"₱"#,##0.00');
                    }
                }
            },
        ];
    }
}
