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
            'A' => 1,
            'B' => 5,
            'C' => 18,
            'D' => 38,
            'E' => 15,
            'F' => 10,
            'G' => 15,
            'H' => 10,
            'I' => 10,
            'J' => 10,
            'K' => 15,
            'L' => 10,
            'M' => 15,
            'N' => 10,
            'O' => 10,
            'P' => 10,
            'Q' => 10,
            'R' => 10,
            'S' => 18,
            'T' => 18,
            'U' => 12,
            'V' => 12,
            'W' => 38,
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
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LEGAL);
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
                foreach ($this->data['groupedEmployees'] as $designationName => $designationData) {
                    if (!$isFirstPage) {
                        $sheet->setBreak('A' . ($currentRow - 1), Worksheet::BREAK_ROW);
                    }
                    $isFirstPage = false;
                    $voucherTotals = $this->data['totalPerVoucher'][$designationName];
                    $designationPap = $designationData['designation_pap'];
                    $offices = $designationData['offices'];
                    $cutoff = $this->data['cutoff'];
                    $this->drawDesignationRow($sheet, $currentRow, $designationName, $designationPap, $voucherTotals, $cutoff);
                    $currentRow++;
                    foreach ($offices as $office) {
                        if (!empty($office['office_name'])) {
                            $this->drawOfficeRow($sheet, $currentRow, $office, $cutoff);
                            $currentRow++;
                        }
                        foreach ($office['employees'] as $employee) {
                            $this->drawEmployeeRow($sheet, $currentRow, $employee, $cutoff);
                            $currentRow++;
                        }
                    }
                    $this->drawTotalRows($sheet, $currentRow, $voucherTotals, $cutoff);
                    $this->drawGrandTotalRows($sheet, $currentRow, $voucherTotals, $cutoff);
                    $this->drawSignatories($sheet, $currentRow, $this->data['assigned']);
                    $currentRow += 2;
                }
                $sheet->setBreak("A{$currentRow}", Worksheet::BREAK_ROW);
                $this->drawOverallTotals($sheet, $currentRow, $this->data['jocosTotal'], $this->data['overallImems'], $this->data['overallTotal'], $this->data['cutoff']);
                $lastRow = $currentRow - 1;
                $sheet->getPageSetup()->setPrintArea("A1:T{$lastRow}");
            },

        ];
    }
    private function drawHeader(Worksheet $sheet, int &$currentRow): void
    {
        $this->addImage($sheet, 'images/bagong_pilipinas.png', 'G2', 70);
        $this->addImage($sheet, 'images/bfar.png', 'H2', 70);
        $this->addImage($sheet, 'images/gad.png', 'M2', 70);
        $sheet->setCellValue('I1', 'Republic of the Philippines');
        $sheet->setCellValue('I2', 'Department of Agriculture');
        $sheet->setCellValue('I3', 'BUREAU OF FISHERIES AND AQUATIC RESOURCES');
        $sheet->setCellValue('I4', 'Region XI, R. Magsaysay Ave., Davao City');
        $sheet->mergeCells('I1:L1')->mergeCells('I2:L2')->mergeCells('I3:L3')->mergeCells('I4:L4');
        $sheet->getStyle('I1:L4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I1:L4')->getFont()->setName('Calibri')->setSize(10);
        $sheet->getStyle('I3')->getFont()->setBold(true);
        $sheet->setCellValue('B6', 'CONTRACT OF SERVICES / JOB ORDER')->getStyle('B6')->getFont()->setBold(true)->setSize(14);
        $sheet->setCellValue('B7', $this->data['dateRange'])->getStyle('B7')->getFont()->setBold(true)->setSize(18);
        $headers = [
            'B8' => 'No.',
            'C8' => 'PAP',
            'D8' => 'Name of Employee',
            'E8' => 'MONTHLY RATE',
            'F8' => 'No. of Working Days',
            'G8' => 'GROSS',
            'H8' => 'Late/Absences',
            'J8' => 'TOTAL',
            'K8' => 'Net of Late/Absences',
            'L8' => 'TAX',
            'M8' => 'Net of Tax',
            'N8' => 'CONTRIBUTIONS',
            'S8' => 'Total Deductions (Contribution)',
            'T8' => 'Net Pay',
            'U8' => 'No of. Cutoff Instances',
            'W8' => 'REMARKS',
        ];
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        $subHeaders = [
            'H9' => 'Absent',
            'I9' => 'Late/Undertime',
            'U9' => 'Absences',
            'V9' => 'Lates',
        ];
        $sheet->getStyle('I9')->getAlignment()->setWrapText(true);
        if ($this->data['cutoff'] === '1-15') {
            $subHeaders['N9'] = 'HDMF-PI';
            $subHeaders['O9'] = 'HDMF-MPL';
            $subHeaders['P9'] = 'HDMF-MP2';
            $subHeaders['Q9'] = 'HDMF-CL';
            $subHeaders['R9'] = 'DARECO';
        } else {
            $subHeaders['N9'] = 'SSS/GSIS Con';
            $subHeaders['O9'] = 'EC Con';
            $subHeaders['P9'] = 'WISP';
            $subHeaders['Q9'] = '';
            $subHeaders['R9'] = '';
        }
        foreach ($subHeaders as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        $sheet->mergeCells('B8:B9')
            ->mergeCells('C8:C9')
            ->mergeCells('D8:D9')
            ->mergeCells('E8:E9')
            ->mergeCells('F8:F9')
            ->mergeCells('G8:G9')
            ->mergeCells('H8:I8')
            ->mergeCells('J8:J9')
            ->mergeCells('K8:K9')
            ->mergeCells('L8:L9')
            ->mergeCells('M8:M9')
            ->mergeCells('N8:R8')
            ->mergeCells('S8:S9')
            ->mergeCells('T8:T9')
            ->mergeCells('U8:V8')
            ->mergeCells('W8:W9');

        $sheet->getStyle('B8:W9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getStyle('B8:W9')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('F8')->getFont()->setBold(false);
        $sheet->getStyle('H9')->getFont()->setBold(false);
        $sheet->getStyle('I9')->getFont()->setBold(false);
        $sheet->getStyle('B8:T9')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]]
        ]);
        $sheet->getStyle('U8:W9')->getFont()->getColor()->setARGB('FFFF0000');
        $sheet->getRowDimension(8)->setRowHeight(35);
        $sheet->getRowDimension(9)->setRowHeight(35);
        $sheet->freezePane('G10');
    }
    private function drawDesignationRow(Worksheet $sheet, int $row, string $designation, ?string $designation_pap, array $voucher, string $cutoff): void
    {
        $sheet->getRowDimension($row)->setRowHeight(25);
        $sheet->getCell("C{$row}")->setValueExplicit((string) ($designation_pap ?? ''), DataType::TYPE_STRING);
        $sheet->getStyle("C{$row}")->getFont()->setBold(true);
        $sheet->setCellValue("D{$row}", $designation);
        $sheet->mergeCells("D{$row}:E{$row}");

        $sheet->setCellValue("F{$row}", '');
        $sheet->setCellValue("G{$row}", $voucher['totalGross'] ? number_format($voucher['totalGross'], 2) : '-');
        $sheet->setCellValue("H{$row}", $voucher['totalAbsent'] ? number_format($voucher['totalAbsent'], 2) : '-');
        $sheet->setCellValue("I{$row}", $voucher['totalLateUndertime'] ? number_format($voucher['totalLateUndertime'], 2) : '-');
        $sheet->setCellValue("J{$row}", $voucher['totalAbsentLate'] ? number_format($voucher['totalAbsentLate'], 2) : '-');
        $sheet->setCellValue("K{$row}", $voucher['totalNetLateAbsences'] ? number_format($voucher['totalNetLateAbsences'], 2) : '-');
        $sheet->setCellValue("L{$row}", $voucher['totalTax'] ? number_format($voucher['totalTax'], 2) : '-');
        $sheet->setCellValue("M{$row}", $voucher['totalNetTax'] ? number_format($voucher['totalNetTax'], 2) : '-');

        if ($cutoff === '1-15') {
            $sheet->setCellValue("N{$row}", $voucher['totalHdmfPi'] ? number_format($voucher['totalHdmfPi'], 2) : '-');
            $sheet->setCellValue("O{$row}", $voucher['totalHdmfMpl'] ? number_format($voucher['totalHdmfMpl'], 2) : '-');
            $sheet->setCellValue("P{$row}", $voucher['totalHdmfMp2'] ? number_format($voucher['totalHdmfMp2'], 2) : '-');
            $sheet->setCellValue("Q{$row}", $voucher['totalHdmfCl'] ? number_format($voucher['totalHdmfCl'], 2) : '-');
            $sheet->setCellValue("R{$row}", $voucher['totalDareco'] ? number_format($voucher['totalDareco'], 2) : '-');
        } elseif ($cutoff === '16-31') {
            $sheet->setCellValue("N{$row}", $voucher['totalSsCon'] ? number_format($voucher['totalSsCon'], 2) : '-');
            $sheet->setCellValue("O{$row}", $voucher['totalEcCon'] ? number_format($voucher['totalEcCon'], 2) : '-');
            $sheet->setCellValue("P{$row}", $voucher['totalWisp'] ? number_format($voucher['totalWisp'], 2) : '-');
            $sheet->setCellValue("Q{$row}", '-');
            $sheet->setCellValue("R{$row}", '-');
        }

        $sheet->setCellValue("S{$row}", $voucher['totalTotalDeduction'] ? number_format($voucher['totalTotalDeduction'], 2) : '-');
        $sheet->setCellValue("T{$row}", $voucher['totalNetPay'] ? number_format($voucher['totalNetPay'], 2) : '-');


        $sheet->getStyle("B{$row}:T{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9EAD3');
        $sheet->getStyle("B{$row}:T{$row}")->getFont()->setBold(true);
        $sheet->getStyle("G{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("B{$row}:T{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }
    private function drawOfficeRow(Worksheet $sheet, int $row, array $office, string $cutoff): void
    {
        $sheet->getCell("C{$row}")->setValueExplicit($office['office_code'], DataType::TYPE_STRING);
        $sheet->setCellValue("D{$row}", $office['office_name']);
        $sheet->mergeCells("D{$row}:E{$row}");

        $sheet->setCellValue("F{$row}", '');
        $sheet->setCellValue("G{$row}", $office['totalGross'] ? number_format($office['totalGross'], 2) : '-');
        $sheet->setCellValue("H{$row}", $office['totalAbsent'] ? number_format($office['totalAbsent'], 2) : '-');
        $sheet->setCellValue("I{$row}", $office['totalLateUndertime'] ? number_format($office['totalLateUndertime'], 2) : '-');
        $sheet->setCellValue("J{$row}", $office['totalAbsentLate'] ? number_format($office['totalAbsentLate'], 2) : '-');
        $sheet->setCellValue("K{$row}", $office['totalNetLateAbsences'] ? number_format($office['totalNetLateAbsences'], 2) : '-');
        $sheet->setCellValue("L{$row}", $office['totalTax'] ? number_format($office['totalTax'], 2) : '-');
        $sheet->setCellValue("M{$row}", $office['totalNetTax'] ? number_format($office['totalNetTax'], 2) : '-');

        if ($cutoff === '1-15') {
            $sheet->setCellValue("N{$row}", $office['totalHdmfPi'] ? number_format($office['totalHdmfPi'], 2) : '-');
            $sheet->setCellValue("O{$row}", $office['totalHdmfMpl'] ? number_format($office['totalHdmfMpl'], 2) : '-');
            $sheet->setCellValue("P{$row}", $office['totalHdmfMp2'] ? number_format($office['totalHdmfMp2'], 2) : '-');
            $sheet->setCellValue("Q{$row}", $office['totalHdmfCl'] ? number_format($office['totalHdmfCl'], 2) : '-');
            $sheet->setCellValue("R{$row}", $office['totalDareco'] ? number_format($office['totalDareco'], 2) : '-');
        } elseif ($cutoff === '16-31') {
            $sheet->setCellValue("N{$row}", $office['totalSsCon'] ? number_format($office['totalSsCon'], 2) : '-');
            $sheet->setCellValue("O{$row}", $office['totalEcCon'] ? number_format($office['totalEcCon'], 2) : '-');
            $sheet->setCellValue("P{$row}", $office['totalWisp'] ? number_format($office['totalWisp'], 2) : '-');
            $sheet->setCellValue("Q{$row}", '-');
            $sheet->setCellValue("R{$row}", '-');
        }

        $sheet->setCellValue("S{$row}", $office['totalTotalDeduction'] ? number_format($office['totalTotalDeduction'], 2) : '-');
        $sheet->setCellValue("T{$row}", $office['totalNetPay'] ? number_format($office['totalNetPay'], 2) : '-');


        $sheet->getStyle("B{$row}:T{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFDEEBF7');
        $sheet->getStyle("G{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("B{$row}:T{$row}")->getFont()->setBold(true);
    }
    private function drawEmployeeRow(Worksheet $sheet, int $row, $employee, string $cutoff): void
    {
        $sheet->getRowDimension($row)->setRowHeight(35);
        $rc = $employee->rawCalculation;
        $name = "{$employee->first_name} " . ($employee->middle_initial ? substr($employee->middle_initial, 0, 1) . '.' : '') . " {$employee->last_name} {$employee->suffix}";
        $sheet->setCellValue("B{$row}", self::$employeeCounter);
        $sheet->setCellValue("D{$row}", $name);
        $sheet->setCellValue("E{$row}", ($employee->monthly_rate ?? 0) > 0 ? number_format($employee->monthly_rate, 2) : '-');
        $sheet->setCellValue("F{$row}", '');
        $sheet->setCellValue("G{$row}", ($employee->gross ?? 0) > 0 ? number_format($employee->gross, 2) : '-');
        $sheet->setCellValue("H{$row}", ($rc?->absent ?? 0) > 0 ? number_format($rc->absent, 2) : '-');
        $sheet->setCellValue("I{$row}", ($rc?->late_undertime ?? 0) > 0 ? number_format($rc->late_undertime, 2) : '-');
        $sheet->setCellValue("J{$row}", ($rc?->total_absent_late ?? 0) > 0 ? number_format($rc->total_absent_late, 2) : '-');
        $sheet->setCellValue("K{$row}", ($rc?->net_late_absences ?? 0) > 0 ? number_format($rc->net_late_absences, 2) : '-');
        $sheet->setCellValue("L{$row}", ($rc?->tax ?? 0) > 0 ? number_format($rc->tax, 2) : '-');
        $sheet->setCellValue("M{$row}", ($rc?->net_tax ?? 0) > 0 ? number_format($rc->net_tax, 2) : '-');

        if ($cutoff === '1-15') {
            $sheet->setCellValue("N{$row}", ($rc?->hdmf_pi ?? 0) > 0 ? number_format($rc->hdmf_pi, 2) : '-');
            $sheet->setCellValue("O{$row}", ($rc?->hdmf_mpl ?? 0) > 0 ? number_format($rc->hdmf_mpl, 2) : '-');
            $sheet->setCellValue("P{$row}", ($rc?->hdmf_mp2 ?? 0) > 0 ? number_format($rc->hdmf_mp2, 2) : '-');
            $sheet->setCellValue("Q{$row}", ($rc?->hdmf_cl ?? 0) > 0 ? number_format($rc->hdmf_cl, 2) : '-');
            $sheet->setCellValue("R{$row}", ($rc?->dareco ?? 0) > 0 ? number_format($rc->dareco, 2) : '-');
        } elseif ($cutoff === '16-31') {
            $sheet->setCellValue("N{$row}", ($rc?->ss_con ?? 0) > 0 ? number_format($rc->ss_con, 2) : '-');
            $sheet->setCellValue("O{$row}", ($rc?->ec_con ?? 0) > 0 ? number_format($rc->ec_con, 2) : '-');
            $sheet->setCellValue("P{$row}", ($rc?->wisp ?? 0) > 0 ? number_format($rc->wisp, 2) : '-');
            $sheet->setCellValue("Q{$row}", '');
            $sheet->setCellValue("R{$row}", '');
        }

        $sheet->setCellValue("S{$row}", ($rc?->total_deduction ?? 0) > 0 ? number_format($rc->total_deduction, 2) : '-');
        $sheet->setCellValue("T{$row}", ($rc?->net_pay ?? 0) > 0 ? number_format($rc->net_pay, 2) : '-');

        $sheet->setCellValue("U{$row}", $rc?->absent_ins ?? '-');
        $sheet->setCellValue("V{$row}", $rc?->late_ins ?? '-');
        $sheet->setCellValue("W{$row}", $rc?->remarks2 ?? '-');
        foreach (['U', 'V', 'W'] as $col) {
            $style = $sheet->getStyle("{$col}{$row}");
            $style->getFont()->getColor()->setARGB(Color::COLOR_RED);
            $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("A{$row}:Z{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        self::$employeeCounter++;
    }
    private function drawTotalRows(Worksheet $sheet, int &$row, array $voucher, string $cutoff): void
    {
        $sheet->getRowDimension($row)->setRowHeight(35);
        $sheet->setCellValue("D{$row}", 'Total');
        $sheet->mergeCells("D{$row}:E{$row}");
        $sheet->setCellValue("F{$row}", '');
        $sheet->setCellValue("G{$row}", ($voucher['totalGross'] ?? 0) > 0 ? number_format($voucher['totalGross'], 2) : '-');
        $sheet->setCellValue("H{$row}", ($voucher['totalAbsent'] ?? 0) > 0 ? number_format($voucher['totalAbsent'], 2) : '-');
        $sheet->setCellValue("I{$row}", ($voucher['totalLateUndertime'] ?? 0) > 0 ? number_format($voucher['totalLateUndertime'], 2) : '-');
        $sheet->setCellValue("J{$row}", ($voucher['totalAbsentLate'] ?? 0) > 0 ? number_format($voucher['totalAbsentLate'], 2) : '-');
        $sheet->setCellValue("K{$row}", ($voucher['totalNetLateAbsences'] ?? 0) > 0 ? number_format($voucher['totalNetLateAbsences'], 2) : '-');
        $sheet->setCellValue("L{$row}", ($voucher['totalTax'] ?? 0) > 0 ? number_format($voucher['totalTax'], 2) : '-');
        $sheet->setCellValue("M{$row}", ($voucher['totalNetTax'] ?? 0) > 0 ? number_format($voucher['totalNetTax'], 2) : '-');
        if ($cutoff === '1-15') {
            $sheet->setCellValue("N{$row}", ($voucher['totalHdmfPi'] ?? 0) > 0 ? number_format($voucher['totalHdmfPi'], 2) : '-');
            $sheet->setCellValue("O{$row}", ($voucher['totalHdmfMpl'] ?? 0) > 0 ? number_format($voucher['totalHdmfMpl'], 2) : '-');
            $sheet->setCellValue("P{$row}", ($voucher['totalHdmfMp2'] ?? 0) > 0 ? number_format($voucher['totalHdmfMp2'], 2) : '-');
            $sheet->setCellValue("Q{$row}", ($voucher['totalHdmfCl'] ?? 0) > 0 ? number_format($voucher['totalHdmfCl'], 2) : '-');
            $sheet->setCellValue("R{$row}", ($voucher['totalDareco'] ?? 0) > 0 ? number_format($voucher['totalDareco'], 2) : '-');
        } elseif ($cutoff === '16-31') {
            $sheet->setCellValue("N{$row}", ($voucher['totalSsCon'] ?? 0) > 0 ? number_format($voucher['totalSsCon'], 2) : '-');
            $sheet->setCellValue("O{$row}", ($voucher['totalEcCon'] ?? 0) > 0 ? number_format($voucher['totalEcCon'], 2) : '-');
            $sheet->setCellValue("P{$row}", ($voucher['totalWisp'] ?? 0) > 0 ? number_format($voucher['totalWisp'], 2) : '-');
            $sheet->setCellValue("Q{$row}", '');
            $sheet->setCellValue("R{$row}", '');
        }
        $sheet->setCellValue("S{$row}", ($voucher['totalTotalDeduction'] ?? 0) > 0 ? number_format($voucher['totalTotalDeduction'], 2) : '-');
        $sheet->setCellValue("T{$row}", ($voucher['totalNetPay'] ?? 0) > 0 ? number_format($voucher['totalNetPay'], 2) : '-');
        $sheet->getStyle("B{$row}:T{$row}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFF00');
        $sheet->getStyle("B{$row}:T{$row}")->getFont()
            ->setBold(true)
            ->setColor(new Color(Color::COLOR_RED));
        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("B{$row}:T{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $row++;
    }
    private function drawGrandTotalRows(Worksheet $sheet, int &$row, array $voucher, string $cutoff): void
    {
        $row++;
        $sheet->getRowDimension($row)->setRowHeight(45);
        $sheet->setCellValue("D{$row}", 'Grand Total');
        $sheet->mergeCells("D{$row}:E{$row}");
        $sheet->setCellValue("F{$row}", '');
        $sheet->setCellValue("G{$row}", ($voucher['totalGross'] ?? 0) > 0 ? number_format($voucher['totalGross'], 2) : '-');
        $sheet->setCellValue("H{$row}", ($voucher['totalAbsent'] ?? 0) > 0 ? number_format($voucher['totalAbsent'], 2) : '-');
        $sheet->setCellValue("I{$row}", ($voucher['totalLateUndertime'] ?? 0) > 0 ? number_format($voucher['totalLateUndertime'], 2) : '-');
        $sheet->setCellValue("J{$row}", ($voucher['totalAbsentLate'] ?? 0) > 0 ? number_format($voucher['totalAbsentLate'], 2) : '-');
        $sheet->setCellValue("K{$row}", ($voucher['totalNetLateAbsences'] ?? 0) > 0 ? number_format($voucher['totalNetLateAbsences'], 2) : '-');
        $sheet->setCellValue("L{$row}", ($voucher['totalTax'] ?? 0) > 0 ? number_format($voucher['totalTax'], 2) : '-');
        $sheet->setCellValue("M{$row}", ($voucher['totalNetTax'] ?? 0) > 0 ? number_format($voucher['totalNetTax'], 2) : '-');
        if ($cutoff === '1-15') {
            $sheet->setCellValue("N{$row}", ($voucher['totalHdmfPi'] ?? 0) > 0 ? number_format($voucher['totalHdmfPi'], 2) : '-');
            $sheet->setCellValue("O{$row}", ($voucher['totalHdmfMpl'] ?? 0) > 0 ? number_format($voucher['totalHdmfMpl'], 2) : '-');
            $sheet->setCellValue("P{$row}", ($voucher['totalHdmfMp2'] ?? 0) > 0 ? number_format($voucher['totalHdmfMp2'], 2) : '-');
            $sheet->setCellValue("Q{$row}", ($voucher['totalHdmfCl'] ?? 0) > 0 ? number_format($voucher['totalHdmfCl'], 2) : '-');
            $sheet->setCellValue("R{$row}", ($voucher['totalDareco'] ?? 0) > 0 ? number_format($voucher['totalDareco'], 2) : '-');
        } elseif ($cutoff === '16-31') {
            $sheet->setCellValue("N{$row}", ($voucher['totalSsCon'] ?? 0) > 0 ? number_format($voucher['totalSsCon'], 2) : '-');
            $sheet->setCellValue("O{$row}", ($voucher['totalEcCon'] ?? 0) > 0 ? number_format($voucher['totalEcCon'], 2) : '-');
            $sheet->setCellValue("P{$row}", ($voucher['totalWisp'] ?? 0) > 0 ? number_format($voucher['totalWisp'], 2) : '-');
            $sheet->setCellValue("Q{$row}", '');
            $sheet->setCellValue("R{$row}", '');
        }
        $sheet->setCellValue("S{$row}", ($voucher['totalTotalDeduction'] ?? 0) > 0 ? number_format($voucher['totalTotalDeduction'], 2) : '-');
        $sheet->setCellValue("T{$row}", ($voucher['totalNetPay'] ?? 0) > 0 ? number_format($voucher['totalNetPay'], 2) : '-');
        $sheet->getStyle("B{$row}:T{$row}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF5B9BD5');
        $sheet->getStyle("B{$row}:T{$row}")->getFont()
            ->setBold(true)
            ->setColor(new Color(Color::COLOR_BLACK));
        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("B{$row}:T{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $row++;
    }
    private function drawOverallTotals(Worksheet $sheet, int &$row, array $jocosTotal, array $overallImems, array $overallTotal, string $cutoff): void
    {
        $row++;
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->setCellValue("D{$row}", 'Gross');
        $sheet->setCellValue("E{$row}", '');
        $sheet->setCellValue("F{$row}", '');
        $sheet->setCellValue("G{$row}", 'Late/Absences');
        $sheet->setCellValue("I{$row}", 'Tax');
        $sheet->setCellValue("J{$row}", '');
        if ($cutoff = '1-15') {
            $sheet->setCellValue("K{$row}", 'HDMF-PI');
            $sheet->setCellValue("L{$row}", 'MPL');
            $sheet->setCellValue("M{$row}", 'MP2');
            $sheet->setCellValue("N{$row}", 'CAL');
            $sheet->setCellValue("O{$row}", 'DARECO');
        } elseif ($cutoff = '1-15') {
            $sheet->setCellValue("K{$row}", 'SSS CON');
            $sheet->setCellValue("L{$row}", 'EC CON');
            $sheet->setCellValue("M{$row}", 'WISP');
            $sheet->setCellValue("N{$row}", '');
            $sheet->setCellValue("O{$row}", '');
        }
        $sheet->setCellValue("P{$row}", 'Total Ded');
        $sheet->setCellValue("Q{$row}", 'NET');
        $style = $sheet->getStyle("D{$row}:Q{$row}");
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2C691');
        $style->getFont()->setBold(true)->setColor(new Color(Color::COLOR_BLACK));
        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_BOTTOM);
        $row++;
        $row++;

        // JO/COS
        $sheet->setCellValue("C{$row}", 'JO/COS');
        $sheet->setCellValue("D{$row}", ($v = $jocosTotal['totalGross'] ?? 0) == 0 ? '-' : number_format($v, 2));
        $sheet->setCellValue("F{$row}", '');
        $sheet->setCellValue("G{$row}", ($v = $jocosTotal['totalAbsentLate'] ?? 0) == 0 ? '-' : number_format($v, 2));
        $sheet->setCellValue("I{$row}", ($v = $jocosTotal['totalTax'] ?? 0) == 0 ? '-' : number_format($v, 2));
        $sheet->setCellValue("J{$row}", '');
        if ($cutoff === '1-15') {
            $sheet->setCellValue("K{$row}", ($v = $jocosTotal['totalHdmfPi'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("L{$row}", ($v = $jocosTotal['totalHdmfMpl'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("M{$row}", ($v = $jocosTotal['totalHdmfMp2'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("N{$row}", ($v = $jocosTotal['totalHdmfCl'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("O{$row}", ($v = $jocosTotal['totalDareco'] ?? 0) == 0 ? '-' : number_format($v, 2));
        } elseif ($cutoff === '16-31') {
            $sheet->setCellValue("K{$row}", ($v = $jocosTotal['totalSsCon'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("L{$row}", ($v = $jocosTotal['totalEcCon'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("M{$row}", ($v = $jocosTotal['totalWisp'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("N{$row}", '-');
            $sheet->setCellValue("O{$row}", '-');
        }
        $sheet->setCellValue("P{$row}", ($v = $jocosTotal['totalTotalDeduction'] ?? 0) == 0 ? '-' : number_format($v, 2));
        $sheet->setCellValue("Q{$row}", ($v = $jocosTotal['totalNetPay'] ?? 0) == 0 ? '-' : number_format($v, 2));
        $numericColumns = ['C', 'D', 'G', 'I', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'];
        foreach ($numericColumns as $col) {
            $sheet->getStyle("{$col}{$row}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // IMEMS
        $row++;
        $sheet->setCellValue("C{$row}", 'IMEMS');
        $sheet->setCellValue("D{$row}", ($v = $overallImems['totalGross'] ?? 0) == 0 ? '-' : number_format($v, 2));
        $sheet->setCellValue("F{$row}", '');
        $sheet->setCellValue("G{$row}", ($v = $overallImems['totalAbsentLate'] ?? 0) == 0 ? '-' : number_format($v, 2));
        $sheet->setCellValue("I{$row}", ($v = $overallImems['totalTax'] ?? 0) == 0 ? '-' : number_format($v, 2));
        $sheet->setCellValue("J{$row}", '');
        if ($cutoff === '1-15') {
            $sheet->setCellValue("K{$row}", ($v = $overallImems['totalHdmfPi'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("L{$row}", ($v = $overallImems['totalHdmfMpl'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("M{$row}", ($v = $overallImems['totalHdmfMp2'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("N{$row}", ($v = $overallImems['totalHdmfCl'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("O{$row}", ($v = $overallImems['totalDareco'] ?? 0) == 0 ? '-' : number_format($v, 2));
        } elseif ($cutoff === '16-31') {
            $sheet->setCellValue("K{$row}", ($v = $overallImems['totalSsCon'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("L{$row}", ($v = $overallImems['totalEcCon'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("M{$row}", ($v = $overallImems['totalWisp'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("N{$row}", '-');
            $sheet->setCellValue("O{$row}", '-');
        }
        $sheet->setCellValue("P{$row}", ($v = $overallImems['totalTotalDeduction'] ?? 0) == 0 ? '-' : number_format($v, 2));
        $sheet->setCellValue("Q{$row}", ($v = $overallImems['totalNetPay'] ?? 0) == 0 ? '-' : number_format($v, 2));
        foreach ($numericColumns as $col) {
            $sheet->getStyle("{$col}{$row}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $row++;
        $sheet->setCellValue("C{$row}", 'SEPARATED');
        $sheet->getStyle("C{$row}")
            ->getFont()->getColor()->setARGB(Color::COLOR_RED);
        $sheet->getStyle("C{$row}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $row++;
        $sheet->setCellValue("C{$row}", 'NEW');
        $sheet->getStyle("C{$row}")
            ->getFont()->getColor()->setARGB(Color::COLOR_RED);
        $sheet->getStyle("C{$row}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        //OVERALL----
        $row++;
        $sheet->setCellValue("D{$row}", ($v = $overallTotal['totalGross'] ?? 0) == 0 ? '-' : number_format($v, 2));
        $sheet->setCellValue("F{$row}", '');
        $sheet->setCellValue("G{$row}", ($v = $overallTotal['totalAbsentLate'] ?? 0) == 0 ? '-' : number_format($v, 2));
        $sheet->setCellValue("I{$row}", ($v = $overallTotal['totalTax'] ?? 0) == 0 ? '-' : number_format($v, 2));
        $sheet->setCellValue("J{$row}", '');
        if ($cutoff === '1-15') {
            $sheet->setCellValue("K{$row}", ($v = $overallTotal['totalHdmfPi'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("L{$row}", ($v = $overallTotal['totalHdmfMpl'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("M{$row}", ($v = $overallTotal['totalHdmfMp2'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("N{$row}", ($v = $overallTotal['totalHdmfCl'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("O{$row}", ($v = $overallTotal['totalDareco'] ?? 0) == 0 ? '-' : number_format($v, 2));
        } elseif ($cutoff === '16-31') {
            $sheet->setCellValue("K{$row}", ($v = $overallTotal['totalSsCon'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("L{$row}", ($v = $overallTotal['totalEcCon'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("M{$row}", ($v = $overallTotal['totalWisp'] ?? 0) == 0 ? '-' : number_format($v, 2));
            $sheet->setCellValue("N{$row}", '-');
            $sheet->setCellValue("O{$row}", '-');
        }
        $sheet->setCellValue("P{$row}", ($v = $overallTotal['totalTotalDeduction'] ?? 0) == 0 ? '-' : number_format($v, 2));
        $sheet->setCellValue("Q{$row}", ($v = $overallTotal['totalNetPay'] ?? 0) == 0 ? '-' : number_format($v, 2));

        $numericColumns = ['D', 'G', 'I', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'];
        foreach ($numericColumns as $col) {
            $sheet->getStyle("{$col}{$row}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        $yellowColumns = ['D', 'G', 'I', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'];
        foreach ($yellowColumns as $col) {
            $style = $sheet->getStyle("{$col}{$row}");
            $style->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('FFFFFF00'); 
            $style->getFont()->setBold(true);
        }
        $row++;


    }
    private function drawSignatories(Worksheet $sheet, int &$row, $assigned): void
    {
        $labelsRow = $row;
        $sheet->setCellValue("D{$labelsRow}", 'Prepared:');
        $sheet->setCellValue("I{$labelsRow}", 'Checked and Noted by:');
        $sheet->setCellValue("N{$labelsRow}", 'Funds Availability:');
        $sheet->setCellValue("R{$labelsRow}", 'Approved:');
        $sheet->getStyle("D{$labelsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("I{$labelsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("N{$labelsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("R{$labelsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $namesRow = $row + 2;


        $sheet->getRowDimension($namesRow)->setRowHeight(35);
        $sheet->getStyle("D{$namesRow}:R{$namesRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
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
