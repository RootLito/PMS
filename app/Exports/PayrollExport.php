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
            'D' => 28,
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
            'U' => 15,
            'V' => 15,
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
                    $this->drawSignatories($sheet, $currentRow, $this->data['assigned']);
                    $currentRow += 2;
                }

                // Overall Totals (COS & IMEMS)
                $this->drawOverallTotals($sheet, $currentRow, $this->data['overallTotal'], $this->data['overallImems'], $this->data['cutoff']);
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
        ];

        // Set main headers
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // --- Sub Headers ---
        $subHeaders = [
            'H9' => 'Absent',
            'I9' => 'Late/Undertime',
        ];

        // Define subheaders for contributions based on cutoff
        if ($this->data['cutoff'] === '1-15') {
            $subHeaders['N9'] = 'HDMF-PI';
            $subHeaders['O9'] = 'HDMF-MPL';
            $subHeaders['P9'] = 'HDMF-MP2';
            $subHeaders['Q9'] = 'HDMF-CL';
            $subHeaders['R9'] = 'DARECO';
        } else { // '16-31' cutoff
            $subHeaders['N9'] = 'SSS/GSIS Con';
            $subHeaders['O9'] = 'EC Con';
            $subHeaders['P9'] = 'WISP';
            $subHeaders['Q9'] = '';
            $subHeaders['R9'] = '';
        }

        // Set subheaders
        foreach ($subHeaders as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // --- Merge cells for main headers and layout ---
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
            ->mergeCells('T8:T9');



        // if ($this->data['cutoff'] === '1-15') {
        //     $sheet->mergeCells('L8:Q8'); 
        // } elseif ($this->data['cutoff'] === '16-31') {
        //     $sheet->mergeCells('L8:Q8'); 
        // }


        $sheet->getStyle('B8:T9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getStyle('B8:T9')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B8:T9')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]]
        ]);
    }

    private function drawDesignationRow(Worksheet $sheet, int $row, string $designation, ?string $designation_pap, array $voucher, string $cutoff): void
{
    $sheet->getRowDimension($row)->setRowHeight(22.5);
    $sheet->getCell("C{$row}")->setValueExplicit((string) ($designation_pap ?? ''), DataType::TYPE_STRING);
    $sheet->getStyle("C{$row}")->getFont()->setBold(true);
    $sheet->setCellValue("D{$row}", $designation);
    $sheet->mergeCells("D{$row}:E{$row}");

    $sheet->setCellValue("F{$row}", '');  
    $sheet->setCellValue("G{$row}", number_format($voucher['totalGross'] ?? 0, 2));
    $sheet->setCellValue("K{$row}", number_format($voucher['totalNetLateAbsences'] ?? 0, 2));
    $sheet->setCellValue("L{$row}", number_format($voucher['totalTax'] ?? 0, 2));
    $sheet->setCellValue("M{$row}", number_format($voucher['totalNetTax'] ?? 0, 2)); 

    if ($cutoff === '1-15') {
        $sheet->setCellValue("N{$row}", number_format($voucher['totalHdmfPi'] ?? 0, 2));
        $sheet->setCellValue("O{$row}", number_format($voucher['totalHdmfMpl'] ?? 0, 2));
        $sheet->setCellValue("P{$row}", number_format($voucher['totalHdmfMp2'] ?? 0, 2));
        $sheet->setCellValue("Q{$row}", number_format($voucher['totalHdmfCl'] ?? 0, 2));
        $sheet->setCellValue("R{$row}", number_format($voucher['totalDareco'] ?? 0, 2));
    } elseif ($cutoff === '16-31') {
        $sheet->setCellValue("N{$row}", number_format($voucher['totalSsCon'] ?? 0, 2));
        $sheet->setCellValue("O{$row}", number_format($voucher['totalEcCon'] ?? 0, 2));
        $sheet->setCellValue("P{$row}", number_format($voucher['totalWisp'] ?? 0, 2));
        $sheet->setCellValue("Q{$row}", '');
        $sheet->setCellValue("R{$row}", '');
    }

    $sheet->setCellValue("S{$row}", number_format($voucher['totalTotalDeduction'] ?? 0, 2));
    $sheet->setCellValue("T{$row}", number_format($voucher['totalNetPay'] ?? 0, 2));

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
    $sheet->setCellValue("G{$row}", number_format($office['totalGross'] ?? 0, 2));
    $sheet->setCellValue("K{$row}", number_format($office['totalNetLateAbsences'] ?? 0, 2));
    $sheet->setCellValue("L{$row}", number_format($office['totalTax'] ?? 0, 2));
    $sheet->setCellValue("M{$row}", number_format($office['totalNetTax'] ?? 0, 2));  

    if ($cutoff === '1-15') {
        $sheet->setCellValue("N{$row}", number_format($office['totalHdmfPi'] ?? 0, 2));
        $sheet->setCellValue("O{$row}", number_format($office['totalHdmfMpl'] ?? 0, 2));
        $sheet->setCellValue("P{$row}", number_format($office['totalHdmfMp2'] ?? 0, 2));
        $sheet->setCellValue("Q{$row}", number_format($office['totalHdmfCl'] ?? 0, 2));
        $sheet->setCellValue("R{$row}", number_format($office['totalDareco'] ?? 0, 2));
    } elseif ($cutoff === '16-31') {
        $sheet->setCellValue("N{$row}", number_format($office['totalSsCon'] ?? 0, 2));
        $sheet->setCellValue("O{$row}", number_format($office['totalEcCon'] ?? 0, 2));
        $sheet->setCellValue("P{$row}", number_format($office['totalWisp'] ?? 0, 2));
        $sheet->setCellValue("Q{$row}", '');
        $sheet->setCellValue("R{$row}", '');
    }

    $sheet->setCellValue("S{$row}", number_format($office['totalTotalDeduction'] ?? 0, 2));
    $sheet->setCellValue("T{$row}", number_format($office['totalNetPay'] ?? 0, 2));

    $sheet->getStyle("B{$row}:T{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFDEEBF7');
    $sheet->getStyle("G{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle("B{$row}:T{$row}")->getFont()->setBold(true);
}


    private function drawEmployeeRow(Worksheet $sheet, int $row, $employee, string $cutoff): void
    {
        $rc = $employee->rawCalculation;
        $name = "{$employee->first_name} " . ($employee->middle_initial ? substr($employee->middle_initial, 0, 1) . '.' : '') . " {$employee->last_name} {$employee->suffix}";
        $sheet->setCellValue("B{$row}", self::$employeeCounter);
        $sheet->setCellValue("D{$row}", $name);
        $sheet->setCellValue("E{$row}", number_format($employee->monthly_rate, 2));
        $sheet->setCellValue("F{$row}", '');
        $sheet->setCellValue("G{$row}", number_format($employee->gross, 2));
        $sheet->setCellValue("H{$row}", number_format($rc?->absent ?? 0, 2));
        $sheet->setCellValue("I{$row}", number_format($rc?->late_undertime ?? 0, 2));
        $sheet->setCellValue("J{$row}", number_format($rc?->total_absent_late ?? 0, 2));
        $sheet->setCellValue("K{$row}", number_format($rc?->net_late_absences ?? 0, 2));
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

        $sheet->setCellValue("S{$row}", number_format($rc?->total_deduction ?? 0, 2));
        $sheet->setCellValue("T{$row}", number_format($rc?->net_pay ?? 0, 2));

        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        self::$employeeCounter++;
    }

    private function drawTotalRows(Worksheet $sheet, int &$row, array $voucher, string $cutoff): void
    {
        $sheet->getRowDimension($row)->setRowHeight(22.5);
        $sheet->setCellValue("D{$row}", 'Total');
        $sheet->mergeCells("D{$row}:E{$row}");

        $sheet->setCellValue("F{$row}", '');
        $sheet->setCellValue("G{$row}", number_format($voucher['totalGross'] ?? 0, 2));
        $sheet->setCellValue("K{$row}", number_format($voucher['totalNetLateAbsences'] ?? 0, 2));
        $sheet->setCellValue("L{$row}", number_format($voucher['totalTax'] ?? 0, 2));

        if ($cutoff === '1-15') {
            $sheet->setCellValue("M{$row}", number_format($voucher['totalHdmfPi'] ?? 0, 2));
            $sheet->setCellValue("N{$row}", number_format($voucher['totalHdmfMpl'] ?? 0, 2));
            $sheet->setCellValue("O{$row}", number_format($voucher['totalHdmfMp2'] ?? 0, 2));
            $sheet->setCellValue("P{$row}", number_format($voucher['totalHdmfCl'] ?? 0, 2));
            $sheet->setCellValue("Q{$row}", number_format($voucher['totalDareco'] ?? 0, 2));
        } elseif ($cutoff === '16-31') {
            $sheet->setCellValue("M{$row}", number_format($voucher['totalSsCon'] ?? 0, 2));
            $sheet->setCellValue("N{$row}", number_format($voucher['totalEcCon'] ?? 0, 2));
            $sheet->setCellValue("O{$row}", number_format($voucher['totalWisp'] ?? 0, 2));
            $sheet->setCellValue("P{$row}", '');
            $sheet->setCellValue("Q{$row}", '');
        }

        $sheet->setCellValue("R{$row}", number_format($voucher['totalTotalDeduction'] ?? 0, 2));
        $sheet->setCellValue("S{$row}", number_format($voucher['totalNetPay'] ?? 0, 2));

        $sheet->getStyle("B{$row}:T{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        $sheet->getStyle("B{$row}:T{$row}")->getFont()->setBold(true)->setColor(new Color(Color::COLOR_RED));
        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("B{$row}:T{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $row++;
    }

    private function drawOverallTotals(Worksheet $sheet, int &$row, array $overallTotal, array $overallImems, string $cutoff): void
    {
        $sheet->getRowDimension($row)->setRowHeight(22.5);
        $sheet->setCellValue("D{$row}", 'GRAND TOTAL');
        $sheet->mergeCells("D{$row}:E{$row}");

        $sheet->setCellValue("F{$row}", '');
        $sheet->setCellValue("G{$row}", number_format($overallTotal['totalGross'] ?? 0, 2));
        $sheet->setCellValue("K{$row}", number_format($overallTotal['totalAbsentLate'] ?? 0, 2));
        $sheet->setCellValue("L{$row}", number_format($overallTotal['totalTax'] ?? 0, 2));

        if ($cutoff === '1-15') {
            $sheet->setCellValue("M{$row}", number_format($overallTotal['totalHdmfPi'] ?? 0, 2));
            $sheet->setCellValue("N{$row}", number_format($overallTotal['totalHdmfMpl'] ?? 0, 2));
            $sheet->setCellValue("O{$row}", number_format($overallTotal['totalHdmfMp2'] ?? 0, 2));
            $sheet->setCellValue("P{$row}", number_format($overallTotal['totalHdmfCl'] ?? 0, 2));
            $sheet->setCellValue("Q{$row}", number_format($overallTotal['totalDareco'] ?? 0, 2));
        } elseif ($cutoff === '16-31') {
            $sheet->setCellValue("M{$row}", number_format($overallTotal['totalSsCon'] ?? 0, 2));
            $sheet->setCellValue("N{$row}", number_format($overallTotal['totalEcCon'] ?? 0, 2));
            $sheet->setCellValue("O{$row}", number_format($overallTotal['totalWisp'] ?? 0, 2));
            $sheet->setCellValue("P{$row}", '');
            $sheet->setCellValue("Q{$row}", '');
        }

        $sheet->setCellValue("R{$row}", number_format($overallTotal['totalTotalDeduction'] ?? 0, 2));
        $sheet->setCellValue("S{$row}", number_format($overallTotal['totalNetPay'] ?? 0, 2));

        $sheet->getStyle("B{$row}:T{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF5B9BD5');
        $sheet->getStyle("B{$row}:T{$row}")->getFont()->setBold(true)->setColor(new Color(Color::COLOR_WHITE));
        $sheet->getStyle("F{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("B{$row}:T{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $row++;

        $sheet->getRowDimension($row)->setRowHeight(22.5);
        $sheet->setCellValue("D{$row}", 'IMEMS');
        $sheet->mergeCells("D{$row}:E{$row}");

        $sheet->setCellValue("F{$row}", '');
        $sheet->setCellValue("G{$row}", number_format($overallImems['totalGross'] ?? 0, 2));
        $sheet->setCellValue("K{$row}", number_format($overallImems['totalAbsentLate'] ?? 0, 2));
        $sheet->setCellValue("L{$row}", number_format($overallImems['totalTax'] ?? 0, 2));

        if ($cutoff === '1-15') {
            $sheet->setCellValue("M{$row}", number_format($overallImems['totalHdmfPi'] ?? 0, 2));
            $sheet->setCellValue("N{$row}", number_format($overallImems['totalHdmfMpl'] ?? 0, 2));
            $sheet->setCellValue("O{$row}", number_format($overallImems['totalHdmfMp2'] ?? 0, 2));
            $sheet->setCellValue("P{$row}", number_format($overallImems['totalHdmfCl'] ?? 0, 2));
            $sheet->setCellValue("Q{$row}", number_format($overallImems['totalDareco'] ?? 0, 2));
        } elseif ($cutoff === '16-31') {
            $sheet->setCellValue("M{$row}", number_format($overallImems['totalSsCon'] ?? 0, 2));
            $sheet->setCellValue("N{$row}", number_format($overallImems['totalEcCon'] ?? 0, 2));
            $sheet->setCellValue("O{$row}", number_format($overallImems['totalWisp'] ?? 0, 2));
            $sheet->setCellValue("P{$row}", '');
            $sheet->setCellValue("Q{$row}", '');
        }

        $sheet->setCellValue("R{$row}", number_format($overallImems['totalTotalDeduction'] ?? 0, 2));
        $sheet->setCellValue("S{$row}", number_format($overallImems['totalNetPay'] ?? 0, 2));

        $sheet->getStyle("B{$row}:T{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFCE4D6');
        $sheet->getStyle("B{$row}:T{$row}")->getFont()->setBold(true);
        $sheet->getStyle("F{$row}:T{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("B{$row}:T{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $row++;
    }







    // private function drawSignatories(Worksheet $sheet, int &$row, $assigned): void
    // {
    //     $labelsRow = $row;

    //     $sheet->setCellValue("D{$labelsRow}", 'Prepared:');
    //     $sheet->setCellValue("I{$labelsRow}", 'Checked and Noted by:');
    //     $sheet->setCellValue("N{$labelsRow}", 'Funds Availability:');
    //     $sheet->setCellValue("R{$labelsRow}", 'Approved:');
    //     $sheet->getStyle("D{$labelsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    //     $sheet->getStyle("I{$labelsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    //     $sheet->getStyle("N{$labelsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    //     $sheet->getStyle("R{$labelsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    //     $namesRow = $row + 2;
    //     $sheet->setCellValue("D{$namesRow}", strtoupper($assigned?->prepared?->name ?? '-'))->getStyle("D{$namesRow}")->getFont()->setBold(true)->setUnderline(true);
    //     $sheet->setCellValue("I{$namesRow}", strtoupper($assigned?->noted?->name ?? '-'))->getStyle("I{$namesRow}")->getFont()->setBold(true)->setUnderline(true);
    //     $sheet->setCellValue("N{$namesRow}", strtoupper($assigned?->funds?->name ?? '-'))->getStyle("N{$namesRow}")->getFont()->setBold(true)->setUnderline(true);
    //     $sheet->setCellValue("R{$namesRow}", strtoupper($assigned?->approved?->name ?? '-'))->getStyle("R{$namesRow}")->getFont()->setBold(true)->setUnderline(true);

    //     $sheet->mergeCells("D{$namesRow}:G{$namesRow}")->getStyle("D{$namesRow}:G{$namesRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    //     $sheet->mergeCells("I{$namesRow}:L{$namesRow}")->getStyle("I{$namesRow}:L{$namesRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    //     $sheet->mergeCells("N{$namesRow}:Q{$namesRow}")->getStyle("N{$namesRow}:Q{$namesRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    //     $sheet->mergeCells("R{$namesRow}:T{$namesRow}")->getStyle("R{$namesRow}:T{$namesRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    //     $designationsRow = $row + 3;
    //     $sheet->setCellValue("D{$designationsRow}", $assigned?->prepared?->designation ?? '-');
    //     $sheet->setCellValue("I{$designationsRow}", $assigned?->noted?->designation ?? '-');
    //     $sheet->setCellValue("N{$designationsRow}", $assigned?->funds?->designation ?? '-');
    //     $sheet->setCellValue("R{$designationsRow}", $assigned?->approved?->designation ?? '-');

    //     $sheet->mergeCells("D{$designationsRow}:G{$designationsRow}")->getStyle("D{$designationsRow}:G{$designationsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    //     $sheet->mergeCells("I{$designationsRow}:L{$designationsRow}")->getStyle("I{$designationsRow}:L{$designationsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    //     $sheet->mergeCells("N{$designationsRow}:Q{$designationsRow}")->getStyle("N{$designationsRow}:Q{$designationsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    //     $sheet->mergeCells("R{$designationsRow}:T{$designationsRow}")->getStyle("R{$designationsRow}:T{$designationsRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    //     $row = $designationsRow;
    // }



    private function drawSignatories(Worksheet $sheet, int &$row, $assigned): void
    {
        $row += 2; // Add some space before signatories
        $labelsRow = $row;
        $sheet->setCellValue("D{$labelsRow}", 'Prepared:');
        $sheet->setCellValue("I{$labelsRow}", 'Checked and Noted by:'); // Updated label
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
