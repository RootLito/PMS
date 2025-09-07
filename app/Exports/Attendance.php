<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Attendance implements FromArray, WithEvents, WithCustomStartCell
{
    protected $groupedEmployees;
    protected $region;
    protected $currentRow = 1;
    protected static $employeeCounter = 1;
    public function __construct(array $groupedEmployees, string $region = 'REGION XI')
    {
        $this->groupedEmployees = $groupedEmployees;
        $this->region = $region;
    }
    public function array(): array
    {
        return [[]];
    }
    public function startCell(): string
    {
        return 'A1';
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $this->setupPage($sheet);
                $this->drawHeader($sheet);
                $this->drawTableHeaders($sheet);

                foreach ($this->groupedEmployees as $designation => $offices) {
                    $this->drawDesignationRow($sheet, $designation);
                    foreach ($offices as $office => $employees) {
                        $this->drawOfficeRow($sheet, $office);
                        foreach ($employees as $employee) {
                            $this->drawEmployeeRow($sheet, $employee);
                        }
                    }
                }

                $this->applyFinalStyling($sheet);
            },
        ];
    }
    private function setupPage(Worksheet $sheet): void
    {
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_LEGAL);

        $columnsWidths = [
            'A' => 6,   // NO.
            'B' => 18,  // LAST NAME
            'C' => 18,  // FIRST NAME
            'D' => 6,   // M.I.
            'E' => 14,  // MONTHLY RATE
            'F' => 8,   // 1st Absence
            'G' => 8,   // 2nd Absence
            'H' => 8,   // Total Absence
            'I' => 8,   // 1st Late
            'J' => 8,   // 2nd Late
            'K' => 8,   // Total Late
            'L' => 60,  // Remarks
        ];
        foreach ($columnsWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
    }
    private function drawHeader(Worksheet $sheet): void
    {
        $formattedDate = \Carbon\Carbon::now()->format('F Y');
        $formattedDate = strtoupper($formattedDate);
        $sheet->mergeCells('A' . $this->currentRow . ':L' . $this->currentRow);
        $sheet->setCellValue('A' . $this->currentRow, 'MONTHLY ATTENDANCE AS OF ' . $formattedDate);
        $sheet->getStyle('A' . $this->currentRow)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $this->currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->currentRow++;
        $sheet->mergeCells('A' . $this->currentRow . ':L' . $this->currentRow);
        $sheet->setCellValue('A' . $this->currentRow, $this->region);
        $sheet->getStyle('A' . $this->currentRow)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $this->currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->currentRow++;

        $this->currentRow++;
    }
    private function drawTableHeaders(Worksheet $sheet): void
    {
        $row1 = $this->currentRow;
        $row2 = $this->currentRow + 1;

        // Set Main Headers (Row 1)
        $sheet->setCellValue("A{$row1}", 'NO.');
        $sheet->setCellValue("B{$row1}", 'LAST NAME');
        $sheet->setCellValue("C{$row1}", 'FIRST NAME');
        $sheet->setCellValue("D{$row1}", 'M.I.');
        $sheet->setCellValue("E{$row1}", 'MONTHLY RATE');
        $sheet->setCellValue("F{$row1}", 'TOTAL INSTANCES (ABSENCES)');
        $sheet->setCellValue("I{$row1}", 'TOTAL INSTANCES (LATES)');
        $sheet->setCellValue("L{$row1}", 'REMARKS for 1st and 2nd Cutoff(specify the absences/lates here)');

        // Merge static headers across 2 rows
        $sheet->mergeCells("A{$row1}:A{$row2}");
        $sheet->mergeCells("B{$row1}:B{$row2}");
        $sheet->mergeCells("C{$row1}:C{$row2}");
        $sheet->mergeCells("D{$row1}:D{$row2}");
        $sheet->mergeCells("E{$row1}:E{$row2}");
        $sheet->mergeCells("L{$row1}:L{$row2}");

        // Merge for ABSENCES and LATES group labels
        $sheet->mergeCells("F{$row1}:H{$row1}");
        $sheet->mergeCells("I{$row1}:K{$row1}");

        // Subheaders for ABSENCES and LATES (Row 2)
        $sheet->setCellValue("F{$row2}", '1st');
        $sheet->setCellValue("G{$row2}", '2nd');
        $sheet->setCellValue("H{$row2}", 'Total');
        $sheet->setCellValue("I{$row2}", '1st');
        $sheet->setCellValue("J{$row2}", '2nd');
        $sheet->setCellValue("K{$row2}", 'Total');

        // Style for all header cells
        $sheet->getStyle("A{$row1}:L{$row2}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row1}:L{$row2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row1}:L{$row2}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Move current row pointer after both header rows
        $this->currentRow = $row2 + 1;
    }
    private function drawDesignationRow(Worksheet $sheet, string $designation): void
    {
        $sheet->mergeCells('A' . $this->currentRow . ':L' . $this->currentRow);
        $sheet->setCellValue('A' . $this->currentRow, $designation);
        $sheet->getStyle('A' . $this->currentRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'italic' => true,
                'color' => ['rgb' => 'C0504D'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF2CC'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        $this->currentRow++;
    }
    private function drawOfficeRow(Worksheet $sheet, string $office): void
    {
        $sheet->mergeCells('A' . $this->currentRow . ':L' . $this->currentRow);
        $sheet->setCellValue('A' . $this->currentRow, $office);
        $sheet->getStyle('A' . $this->currentRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'italic' => true,
                'color' => ['rgb' => '4F81BD'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DCE6F1'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        $this->currentRow++;
    }
    private function drawEmployeeRow(Worksheet $sheet, array $employee): void
    {
        $absent_1 = is_numeric($employee['absent_1']) ? (float) $employee['absent_1'] : 0;
        $absent_2 = is_numeric($employee['absent_2']) ? (float) $employee['absent_2'] : 0;
        $late_1 = is_numeric($employee['late_1']) ? (float) $employee['late_1'] : 0;
        $late_2 = is_numeric($employee['late_2']) ? (float) $employee['late_2'] : 0;
        $absentTotal = $absent_1 + $absent_2;
        $lateTotal = $late_1 + $late_2;
        $sheet->setCellValue('A' . $this->currentRow, self::$employeeCounter++);
        $sheet->setCellValue('B' . $this->currentRow, $employee['last_name']);
        $sheet->setCellValue('C' . $this->currentRow, $employee['first_name']);
        $sheet->setCellValue('D' . $this->currentRow, $employee['middle_initial'] ?? '');
        $sheet->setCellValue('E' . $this->currentRow, is_numeric($employee['monthly_rate']) ? number_format($employee['monthly_rate'], 2) : '-');
        $sheet->setCellValue('F' . $this->currentRow, $absent_1 > 0 ? $absent_1 : '');
        $sheet->setCellValue('G' . $this->currentRow, $absent_2 > 0 ? $absent_2 : '');
        $sheet->setCellValue('H' . $this->currentRow, $absentTotal > 0 ? $absentTotal : '-');
        $sheet->setCellValue('I' . $this->currentRow, $late_1 > 0 ? $late_1 : '');
        $sheet->setCellValue('J' . $this->currentRow, $late_2 > 0 ? $late_2 : '');
        $sheet->setCellValue('K' . $this->currentRow, $lateTotal > 0 ? $lateTotal : '-');
        $sheet->setCellValue('L' . $this->currentRow, $employee['remarks'] ?? '');
        $sheet->getStyle("A{$this->currentRow}:L{$this->currentRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("E{$this->currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("F{$this->currentRow}:K{$this->currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("L{$this->currentRow}")->getAlignment()->setWrapText(true);
        $this->currentRow++;
    }
    private function applyFinalStyling(Worksheet $sheet): void
    {
        $lastRow = $this->currentRow - 1;
        $sheet->getStyle("A{$lastRow}:L{$lastRow}")
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->freezePane('A6');
        $sheet->getStyle("A1:L{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);
        for ($i = 1; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(-1);
        }
    }
}
