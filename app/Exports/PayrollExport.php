<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class PayrollExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    public function collection()
    {
        // No data yet, empty collection
        return collect([]);
    }

    public function headings(): array
    {
        // Empty headings for now
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'B1:T1' => ['alignment' => ['horizontal' => 'center'], 'font' => ['bold' => true, 'size' => 12]],
            'B2:T2' => ['alignment' => ['horizontal' => 'center'], 'font' => ['bold' => true, 'size' => 12]],
            'B3:T3' => ['alignment' => ['horizontal' => 'center'], 'font' => ['bold' => true, 'size' => 12]],
            'B4:T4' => ['alignment' => ['horizontal' => 'center'], 'font' => ['bold' => false, 'italic' => true, 'size' => 10]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;

                // Merge B1 to T1, B2 to T2, B3 to T3, B4 to T4
                $sheet->mergeCells('B1:T1');
                $sheet->mergeCells('B2:T2');
                $sheet->mergeCells('B3:T3');
                $sheet->mergeCells('B4:T4');

                // Set text for merged cells
                $sheet->setCellValue('B1', 'Republic of the Philippines');
                $sheet->setCellValue('B2', 'Department of Agriculture');
                $sheet->setCellValue('B3', 'BUREAU OF FISHERIES AND AQUATIC RESOURCES');
                $sheet->setCellValue('B4', 'Region XI, R. Magsaysay Ave., Davao City');

                // Optional: Set row heights for headers
                $sheet->getRowDimension(1)->setRowHeight(20);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(20);
            }
        ];
    }
}
