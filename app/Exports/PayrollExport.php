<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
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
        self::$employeeCounter = 1;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 1, 'B' => 5, 'C' => 18, 'D' => 28, 'E' => 15, 'F' => 10,
            'G' => 15, 'H' => 10, 'I' => 10, 'J' => 10, 'K' => 15, 'L' => 10,
            'M' => 15, 'N' => 10, 'O' => 10, 'P' => 10, 'Q' => 10, 'R' => 10,
            'S' => 18, 'T' => 18, 'U' => 15, 'V' => 15,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $currentRow = 1;

                // --- GLOBAL FONT STYLE ---
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial Narrow')->setSize(14);

                // --- PRINTING SETUP ---
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.748031496062992);
                $sheet->getPageMargins()->setBottom(0.748031496062992);
                $sheet->getPageMargins()->setLeft(0.708661417322835);
                $sheet->getPageMargins()->setRight(0.708661417322835);
                $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 9);

                // --- GENERAL SETUP ---
                $sheet->setShowGridlines(false);
                $sheet->getHeaderFooter()->setOddFooter('&CPage &P of &N');
                $sheet->getHeaderFooter()->setEvenFooter('&CPage &P of &N');

                $this->drawHeader($sheet, $currentRow);
                $currentRow = 10;
                $isFirstPage = true;

                foreach ($this->data['groupedEmployees'] as $designation => $offices) {
                    if (!$isFirstPage) {
                        $sheet->setBreak('A' . ($currentRow - 1), Worksheet::BREAK_ROW);
                    }
                    $isFirstPage = false;

                    $voucher = $this->data['totalPerVoucher'][$designation];
                    $this->drawDesignationRow($sheet, $currentRow, $designation, $offices->first()['office_code'], $voucher);
                    $currentRow++;

                    foreach ($offices as $office) {
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
                    $this->drawSignatories($sheet, $currentRow, $this->data['assigned']);
                    $currentRow += 2;
                }
            },
        ];
    }

    private function drawHeader(Worksheet $sheet, int &$currentRow): void
    {
        $this->addImage($sheet, 'images/bagong_pilipinas.png', 'G2', 70);
        $this->addImage($sheet, 'images/bfar.png', 'H2', 70);
        $this->addImage($sheet, 'images/gad.png', 'L2', 70);

        // --- Top Right Header (Calibri, 10) ---
        $sheet->setCellValue('I1', 'Republic of the Philippines');
        $sheet->setCellValue('I2', 'Department of Agriculture');
        $sheet->setCellValue('I3', 'BUREAU OF FISHERIES AND AQUATIC RESOURCES');
        $sheet->setCellValue('I4', 'Region XI, R. Magsaysay Ave., Davao City');
        $sheet->mergeCells('I1:L1')->mergeCells('I2:L2')->mergeCells('I3:L3')->mergeCells('I4:L4');
        $sheet->getStyle('I1:L4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I1:L4')->getFont()->setName('Calibri')->setSize(10);
        $sheet->getStyle('I3')->getFont()->setBold(true);

        // --- Main Title & Date (Arial Narrow) ---
        $sheet->setCellValue('B6', 'CONTRACT OF SERVICES / JOB ORDER')->getStyle('B6')->getFont()->setBold(true)->setSize(14);
        $sheet->setCellValue('B7', $this->data['dateRange'])->getStyle('B7')->getFont()->setBold(true)->setSize(18);

        // --- Main Table Headers ---
        $headers = [
            'B8' => 'No.', 'C8' => 'PAP', 'D8' => 'Name of Employee', 'E8' => 'MONTHLY RATE', 'F8' => 'No. of Working Days',
            'G8' => 'GROSS', 'H8' => 'Late/Absences', 'J8' => 'Total', 'K8' => 'Net of Late/Absences',
            'L8' => 'TAX', 'M8' => 'CONTRIBUTIONS', 'S8' => 'Total Deductions (Contribution)', 'T8' => 'Net Pay'
        ];
        foreach ($headers as $cell => $value) $sheet->setCellValue($cell, $value);

        $subHeaders = [
            'H9' => 'Absent', 'I9' => 'Late/ Undertime', 'M9' => 'HDMF-PI', 'N9' => 'HDMF-MPL',
            'O9' => 'HDMF-MP2', 'P9' => 'HDMF-CL', 'Q9' => 'DARECO'
        ];
        foreach ($subHeaders as $cell => $value) $sheet->setCellValue($cell, $value);

        $sheet->mergeCells('B8:B9')->mergeCells('C8:C9')->mergeCells('D8:D9')->mergeCells('E8:E9')->mergeCells('F8:F9')
            ->mergeCells('G8:G9')->mergeCells('H8:I8')->mergeCells('J8:J9')->mergeCells('K8:K9')->mergeCells('L8:L9')
            ->mergeCells('M8:R8')->mergeCells('S8:S9')->mergeCells('T8:T9');

        $sheet->getStyle('B8:T9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getStyle('B8:T9')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B8:T9')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]]
        ]);
    }

    private function drawDesignationRow(Worksheet $sheet, int $row, string $designation, string $code, array $voucher): void
    {
        $sheet->getRowDimension($row)->setRowHeight(22.5);
        $sheet->getCell("C{$row}")->setValueExplicit($code, DataType::TYPE_STRING);
        $sheet->getStyle("C{$row}")->getFont()->setBold(true);
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
        $sheet->getStyle("B{$row}:T{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    private function drawOfficeRow(Worksheet $sheet, int $row, array $office): void
    {
        $sheet->getCell("C{$row}")->setValueExplicit($office['office_code'], DataType::TYPE_STRING);
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
        $sheet->getRowDimension($row)->setRowHeight(22.5);
        $sheet->setCellValue("D{$row}", 'Total');
        $sheet->setCellValue("G{$row}", number_format($voucher['totalGross'] ?? 0, 2));
        $sheet->setCellValue("K{$row}", number_format($voucher['totalNetLateAbsences'] ?? 0, 2));
        $sheet->setCellValue("M{$row}", number_format($voucher['totalHdmfPi'] ?? 0, 2));
        $sheet->setCellValue("N{$row}", number_format($voucher['totalHdmfMpl'] ?? 0, 2));
        $sheet->setCellValue("S{$row}", number_format($voucher['totalTotalDeduction'] ?? 0, 2));
        $sheet->setCellValue("T{$row}", number_format($voucher['totalNetPay'] ?? 0, 2));
        $sheet->getStyle("B{$row}:T{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        $sheet->getStyle("B{$row}:T{$row}")->getFont()->setBold(true)->setColor(new Color(Color::COLOR_RED));
        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("B{$row}:T{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $row++;
        $sheet->getRowDimension($row)->setRowHeight(22.5);
        $sheet->setCellValue("D{$row}", 'GRAND TOTAL');
        $sheet->setCellValue("G{$row}", number_format($voucher['totalGross'] ?? 0, 2));
        $sheet->setCellValue("K{$row}", number_format($voucher['totalNetLateAbsences'] ?? 0, 2));
        $sheet->setCellValue("M{$row}", number_format($voucher['totalHdmfPi'] ?? 0, 2));
        $sheet->setCellValue("N{$row}", number_format($voucher['totalHdmfMpl'] ?? 0, 2));
        $sheet->setCellValue("S{$row}", number_format($voucher['totalTotalDeduction'] ?? 0, 2));
        $sheet->setCellValue("T{$row}", number_format($voucher['totalNetPay'] ?? 0, 2));
        $sheet->getStyle("B{$row}:T{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF5B9BD5');
        $sheet->getStyle("B{$row}:T{$row}")->getFont()->setBold(true)->setColor(new Color(Color::COLOR_WHITE));
        $sheet->getStyle("G{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("B{$row}:T{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $row++;
    }

    private function drawSignatories(Worksheet $sheet, int &$row, $assigned): void
    {
        $labelsRow = $row;
        $sheet->setCellValue("D{$labelsRow}", 'Prepared:');
        $sheet->setCellValue("I{$labelsRow}", 'Noted by:');
        $sheet->setCellValue("N{$labelsRow}", 'Funds Availability:');
        $sheet->setCellValue("R{$labelsRow}", 'Approved:');
        $sheet->getStyle("D{$labelsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("I{$labelsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("N{$labelsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("R{$labelsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $namesRow = $row + 2;
        $sheet->setCellValue("D{$namesRow}", strtoupper($assigned?->prepared?->name ?? '-'))->getStyle("D{$namesRow}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->setCellValue("I{$namesRow}", strtoupper($assigned?->noted?->name ?? '-'))->getStyle("I{$namesRow}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->setCellValue("N{$namesRow}", strtoupper($assigned?->funds?->name ?? '-'))->getStyle("N{$namesRow}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->setCellValue("R{$namesRow}", strtoupper($assigned?->approved?->name ?? '-'))->getStyle("R{$namesRow}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->mergeCells("D{$namesRow}:G{$namesRow}")->getStyle("D{$namesRow}:G{$namesRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->mergeCells("I{$namesRow}:L{$namesRow}")->getStyle("I{$namesRow}:L{$namesRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->mergeCells("N{$namesRow}:Q{$namesRow}")->getStyle("N{$namesRow}:Q{$namesRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->mergeCells("R{$namesRow}:T{$namesRow}")->getStyle("R{$namesRow}:T{$namesRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $designationsRow = $row + 3;
        $sheet->setCellValue("D{$designationsRow}", $assigned?->prepared?->designation ?? '-');
        $sheet->setCellValue("I{$designationsRow}", $assigned?->noted?->designation ?? '-');
        $sheet->setCellValue("N{$designationsRow}", $assigned?->funds?->designation ?? '-');
        $sheet->setCellValue("R{$designationsRow}", $assigned?->approved?->designation ?? '-');
        $sheet->mergeCells("D{$designationsRow}:G{$designationsRow}")->getStyle("D{$designationsRow}:G{$designationsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->mergeCells("I{$designationsRow}:L{$designationsRow}")->getStyle("I{$designationsRow}:L{$designationsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->mergeCells("N{$designationsRow}:Q{$designationsRow}")->getStyle("N{$designationsRow}:Q{$designationsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->mergeCells("R{$designationsRow}:T{$designationsRow}")->getStyle("R{$designationsRow}:T{$designationsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $row = $designationsRow;
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