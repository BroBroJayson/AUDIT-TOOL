<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DR_SALES_Export implements FromCollection, WithStyles, WithHeadings, WithEvents, WithCustomStartCell
{
    protected $comparisonResults;
    protected $dateRange;

    public function __construct($comparisonResults, $dateRange)
    {
        $this->comparisonResults = $comparisonResults;
        $this->dateRange = $dateRange;
    }

    public function collection()
    {
        $exportData = [];

        foreach ($this->comparisonResults as $index => $result) {
            $exportData[] = [
                'index' => $index + 1,
                'item_code' => $result['item_code'],
                'item_name' => $result['item_name'],
                'uom' => $result['uom'],
                'dr#' => $result['dr_number'],
                'dr' => $result['file1_qty'],
                'dr-rcv#' => '',
                'dr-rvc' => ''
            ];

            foreach ($result['matching_rows'] as $match) {
                $exportData[] = [
                    'index' => '',
                    'item_code' => $match['item_code'],
                    'item_name' => $match['item_name'],
                    'uom' => $match['uom'],
                    'dr#' => '',
                    'dr' => '',
                    'dr-rcv#' => $match['file_1_number'],
                    'dr-rcv' => $match['qty']
                ];
            }
        }

        return new Collection($exportData);
    }

    public function headings(): array
    {
        return [
            'NO.', 'ITEM CODE', 'ITEM NAME', 'UOM', 'DR#', 'DR', 'DR-RCV#', 'DR-RCV'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2 => ['font' => ['bold' => true]], // Header row (starts at A2)
        ];
    }

    public function startCell(): string
    {
        return 'A2'; // Headers start at row 2
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $title = 'DELIVERY VERSUS DELIVERY RECEIVE COMPARISON ' . $this->dateRange;
                $event->sheet->setCellValue('A1', $title);
            },

            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Merge title row
                $sheet->mergeCells('A1:H1');

                // Style title
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['argb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => '209cf5'],
                    ],
                ]);

                // Style header row
                $sheet->getStyle('A2:H2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => '209cf5'],
                    ],
                ]);

                // Auto-size columns
                foreach (range('A', 'H') as $col) {
                    $sheet->getDelegate()->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
