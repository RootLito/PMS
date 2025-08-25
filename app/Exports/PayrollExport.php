<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;

class PayrollExport implements WithEvents, WithColumnWidths
{
    protected $data;
    protected static $employeeCounter = 1;

    public function __construct(array $data)
    {
        $this->data = $data;
        self::$employeeCounter = 1; // Reset counter for each new export instance
    }

    public function columnWidths(): array
    {
        return [
            'A' => 1, 'B' => 5, 'C' => 18, 'D' => 28, 'E' => 15, 'F' => 10,
            'G' => 15, 'H' => 10, 'I' => 10, 'J' => 10, 'K' => 15, 'L' => 10,
            'M' => 15, 'N' => 10, 'O' => 10, 'P' => 10, 'Q' => 10, 'R' => 10,
            'S' => 18, 'T' => 18,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $currentRow = 1;

                $this->drawHeader($sheet, $currentRow);
                $currentRow = 10;

                foreach ($this->data['groupedEmployees'] as $designation => $offices) {
                    $voucher = $this->data['totalPerVoucher'][$designation];
                    
                    $this->drawDesignationRow($sheet, $currentRow, $designation, $offices->first()['office_code'], $voucher);
                    $currentRow++;

                    foreach ($offices as $office) {
                        // We only draw the office row if it has a name
                        if (!empty($office['office_name'])) {
                            $this->drawOfficeRow($sheet, $currentRow, $office);
                            $currentRow++;
                        }

                        foreach ($office['employees'] as $employee) {
                            $this->drawEmployeeRow($sheet, $currentRow, $employee);
                            $currentRow++;
                        }
                    }

                    $this->drawTotalRows($sheet, $currentRow, $voucher);
                    $currentRow += 3;

                    $this->drawSignatories($sheet, $currentRow, $this->data['assigned']);
                    $currentRow += 6;

                    $sheet->getRowDimension($currentRow)->setRowHeight(15);
                    $currentRow++;
                }

                $sheet->getStyle('B8:T' . ($currentRow - 3))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]]
                ]);
            },
        ];
    }

    private function drawHeader(Worksheet $sheet, int &$currentRow): void
    {
        // Logos
        $this->addImage($sheet, 'images/bagong_pilipinas.png', 'G2', 70);
        $this->addImage($sheet, 'images/bfar.png', 'H2', 70);
        $this->addImage($sheet, 'images/gad.png', 'L2', 70);
        
        $sheet->setCellValue('I1', 'Republic of the Philippines');
        $sheet->setCellValue('I2', 'Department of Agriculture');
        $sheet->setCellValue('I3', 'BUREAU OF FISHERIES AND AQUATIC RESOURCES')->getStyle('I3')->getFont()->setBold(true);
        $sheet->setCellValue('I4', 'Region XI, R. Magsaysay Ave., Davao City');
        $sheet->mergeCells('I1:L1')->mergeCells('I2:L2')->mergeCells('I3:L3')->mergeCells('I4:L4');
        $sheet->getStyle('I1:L4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I1:L4')->getFont()->setSize(9);

        $sheet->setCellValue('B6', 'CONTRACT OF SERVICES / JOB ORDER')->getStyle('B6')->getFont()->setBold(true);
        $sheet->setCellValue('B7', $this->data['dateRange'])->getStyle('B7')->getFont()->setBold(true);

        $headers = [
            'B8' => 'No.', 'C8' => 'PAP', 'D8' => 'Name of Employee', 'E8' => 'MONTHLY RATE',
            'G8' => 'GROSS', 'H8' => 'Late/Absences', 'K8' => 'Net of Late/Absences',
            'L8' => 'TAX', 'M8' => 'CONTRIBUTIONS', 'S8' => 'Total Deductions (Contribution)', 'T8' => 'Net Pay'
        ];
        foreach ($headers as $cell => $value) $sheet->setCellValue($cell, $value);

        $subHeaders = [
            'H9' => 'Absent', 'I9' => 'Late/ Undertime', 'J9' => 'Total',
            'M9' => 'HDMF-PI', 'N9' => 'HDMF-MPL', 'O9' => 'HDMF-MP2',
            'P9' => 'HDMF-CL', 'Q9' => 'DARECO'
        ];
        foreach ($subHeaders as $cell => $value) $sheet->setCellValue($cell, $value);

        $sheet->mergeCells('B8:B9')->mergeCells('C8:C9')->mergeCells('D8:D9')->mergeCells('E8:E9')->mergeCells('F8:F9')
            ->mergeCells('G8:G9')->mergeCells('H8:J8')->mergeCells('K8:K9')->mergeCells('L8:L9')
            ->mergeCells('M8:R8')->mergeCells('S8:S9')->mergeCells('T8:T9');
        
        $sheet->getStyle('B8:T9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getStyle('B8:T9')->getFont()->setBold(true)->setSize(9);
    }

    private function drawDesignationRow(Worksheet $sheet, int $row, string $designation, string $code, array $voucher): void
    {
        $sheet->setCellValue("C{$row}", $code)->getStyle("C{$row}")->getFont()->setBold(true);
        $sheet->setCellValue("D{$row}", $designation);
        $sheet->mergeCells("D{$row}:F{$row}");
        
        $sheet->setCellValue("G{$row}", number_format($voucher['totalGross'] ?? 0, 2));
        $sheet->setCellValue("K{$row}", number_format($voucher['totalNetLateAbsences'] ?? 0, 2));
        $sheet->setCellValue("M{$row}", number_format($voucher['totalHdmfPi'] ?? 0, 2));
        $sheet->setCellValue("N{$row}", number_format($voucher['totalHdmfMpl'] ?? 0, 2));
        $sheet->setCellValue("S{$row}", number_format($voucher['totalTotalDeduction'] ?? 0, 2));
        $sheet->setCellValue("T{$row}", number_format($voucher['totalNetPay'] ?? 0, 2));
        
        $sheet->getStyle("B{$row}:T{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9EAD3');
        $sheet->getStyle("B{$row}:T{$row}")->getFont()->setBold(true);
        $sheet->getStyle("G{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }
    
    private function drawOfficeRow(Worksheet $sheet, int $row, array $office): void
    {
        $sheet->setCellValue("C{$row}", $office['office_code']);
        $sheet->setCellValue("D{$row}", $office['office_name']);
        $sheet->mergeCells("D{$row}:F{$row}");

        $sheet->setCellValue("G{$row}", number_format($office['totalGross'] ?? 0, 2));
        $sheet->setCellValue("K{$row}", number_format($office['totalNetLateAbsences'] ?? 0, 2));
        $sheet->setCellValue("M{$row}", number_format($office['totalHdmfPi'] ?? 0, 2));
        $sheet->setCellValue("N{$row}", number_format($office['totalHdmfMpl'] ?? 0, 2));
        $sheet->setCellValue("S{$row}", number_format($office['totalTotalDeduction'] ?? 0, 2));
        $sheet->setCellValue("T{$row}", number_format($office['totalNetPay'] ?? 0, 2));

        $sheet->getStyle("B{$row}:T{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFDEEBF7');
        $sheet->getStyle("G{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    private function drawEmployeeRow(Worksheet $sheet, int $row, $employee): void
    {
        $rc = $employee->rawCalculation;
        $name = "{$employee->first_name} " . ($employee->middle_initial ? substr($employee->middle_initial, 0, 1) . '.' : '') . " {$employee->last_name} {$employee->suffix}";
        
        $sheet->setCellValue("B{$row}", self::$employeeCounter);
        $sheet->setCellValue("D{$row}", $name);
        $sheet->setCellValue("E{$row}", number_format($employee->monthly_rate, 2));
        $sheet->setCellValue("G{$row}", number_format($employee->gross, 2));
        $sheet->setCellValue("K{$row}", number_format($rc?->net_late_absences ?? 0, 2));
        $sheet->setCellValue("M{$row}", ($rc?->hdmf_pi ?? 0) > 0 ? number_format($rc->hdmf_pi, 2) : '-');
        $sheet->setCellValue("N{$row}", ($rc?->hdmf_mpl ?? 0) > 0 ? number_format($rc->hdmf_mpl, 2) : '-');
        $sheet->setCellValue("S{$row}", number_format($rc?->total_deduction ?? 0, 2));
        $sheet->setCellValue("T{$row}", number_format($rc?->net_pay ?? 0, 2));
        
        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        self::$employeeCounter++;
    }

    private function drawTotalRows(Worksheet $sheet, int &$row, array $voucher): void
    {
        $sheet->setCellValue("D{$row}", 'Total');
        $sheet->setCellValue("G{$row}", number_format($voucher['totalGross'] ?? 0, 2));
        $sheet->setCellValue("K{$row}", number_format($voucher['totalNetLateAbsences'] ?? 0, 2));
        $sheet->setCellValue("M{$row}", number_format($voucher['totalHdmfPi'] ?? 0, 2));
        $sheet->setCellValue("N{$row}", number_format($voucher['totalHdmfMpl'] ?? 0, 2));
        $sheet->setCellValue("S{$row}", number_format($voucher['totalTotalDeduction'] ?? 0, 2));
        $sheet->setCellValue("T{$row}", number_format($voucher['totalNetPay'] ?? 0, 2));
        
        $sheet->getStyle("B{$row}:T{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        // --- FIX IS HERE ---
        $sheet->getStyle("B{$row}:T{$row}")->getFont()->setBold(true)->setColor(new Color(Color::COLOR_RED));
        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $row++;

        $sheet->setCellValue("D{$row}", 'GRAND TOTAL');
        $sheet->setCellValue("G{$row}", number_format($voucher['totalGross'] ?? 0, 2));
        $sheet->setCellValue("K{$row}", number_format($voucher['totalNetLateAbsences'] ?? 0, 2));
        $sheet->setCellValue("M{$row}", number_format($voucher['totalHdmfPi'] ?? 0, 2));
        $sheet->setCellValue("N{$row}", number_format($voucher['totalHdmfMpl'] ?? 0, 2));
        $sheet->setCellValue("S{$row}", number_format($voucher['totalTotalDeduction'] ?? 0, 2));
        $sheet->setCellValue("T{$row}", number_format($voucher['totalNetPay'] ?? 0, 2));
        
        $sheet->getStyle("B{$row}:T{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF5B9BD5');
        // --- AND FIX IS HERE ---
        $sheet->getStyle("B{$row}:T{$row}")->getFont()->setBold(true)->setColor(new Color(Color::COLOR_WHITE));
        $sheet->getStyle("G{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }
    
    private function drawSignatories(Worksheet $sheet, int &$row, $assigned): void
    {
        $row++;
        $sheet->setCellValue("B{$row}", 'Prepared:');
        $sheet->setCellValue("H{$row}", 'Noted by:');
        $sheet->setCellValue("M{$row}", 'Funds Availability:');
        $sheet->setCellValue("S{$row}", 'Approved:');
        $row += 2;

        $sheet->setCellValue("B{$row}", strtoupper($assigned?->prepared?->name ?? '-'))->getStyle("B{$row}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->setCellValue("H{$row}", strtoupper($assigned?->noted?->name ?? '-'))->getStyle("H{$row}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->setCellValue("M{$row}", strtoupper($assigned?->funds?->name ?? '-'))->getStyle("M{$row}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->setCellValue("S{$row}", strtoupper($assigned?->approved?->name ?? '-'))->getStyle("S{$row}")->getFont()->setBold(true)->setUnderline(true);
        $row++;

        $sheet->setCellValue("B{$row}", $assigned?->prepared?->designation ?? '-');
        $sheet->setCellValue("H{$row}", $assigned?->noted?->designation ?? '-');
        $sheet->setCellValue("M{$row}", $assigned?->funds?->designation ?? '-');
        $sheet->setCellValue("S{$row}", $assigned?->approved?->designation ?? '-');
        
        $sheet->mergeCells("B{$row}:F{$row}")->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("B" . ($row-1) . ":F" . ($row-1))->getStyle("B" . ($row-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("B" . ($row-3) . ":F" . ($row-3))->getStyle("B" . ($row-3))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells("H{$row}:L{$row}")->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("H" . ($row-1) . ":L" . ($row-1))->getStyle("H" . ($row-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("H" . ($row-3) . ":L" . ($row-3))->getStyle("H" . ($row-3))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells("M{$row}:R{$row}")->getStyle("M{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("M" . ($row-1) . ":R" . ($row-1))->getStyle("M" . ($row-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("M" . ($row-3) . ":R" . ($row-3))->getStyle("M" . ($row-3))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->mergeCells("S{$row}:T{$row}")->getStyle("S{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("S" . ($row-1) . ":T" . ($row-1))->getStyle("S" . ($row-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("S" . ($row-3) . ":T" . ($row-3))->getStyle("S" . ($row-3))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }

    private function addImage(Worksheet $sheet, string $path, string $coordinates, int $height): void
    {
        if (file_exists(public_path($path))) {
            $drawing = new Drawing();
            $drawing->setPath(public_path($path));
            $drawing->setCoordinates($coordinates);
            $drawing->setHeight($height);
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        }
    }
}