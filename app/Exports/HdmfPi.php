<?php

namespace App\Exports;

use App\Models\Contribution;
use App\Models\Assigned;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class HdmfPi implements FromArray, WithEvents, WithCustomStartCell
{
    public $signatories;

    protected $headerInfo = [
        'employer_id' => '210457140007',
        'employer_name' => 'JO-BUREAU OF FISHERIES AND AQUATIC RESOURCES XI',
        'address' => 'RAMON MAGSAYSAY AVENUE, DAVAO CITY',
        'contact' => '09915148718',
        'email' => 'hrms.region11@bfar.da.gov.ph',
        'total' => 0,
    ];

    protected $startRow = 9;

    public function __construct()
    {
        $this->signatories = Assigned::with(['prepared', 'noted', 'funds', 'approved'])->latest()->first();
    }

    public function array(): array
    {
        $contributions = Contribution::with('employee')
            ->where(function ($query) {
                $query->whereNotNull('hdmf_pi')
                    ->orWhereNotNull('hdmf_mp2');
            })
            ->join('employees', 'contributions.employee_id', '=', 'employees.id')
            ->orderBy('employees.last_name', 'asc')
            ->select('contributions.*', 'employees.monthly_rate')
            ->get();

        $data = [];
        $runningTotal = 0;

        // Custom totals calculated in PHP to avoid Excel formula errors with strings
        $this->totalM2 = 0;
        $this->totalMin = 0;
        $this->totalMoreThanMin = 0;
        $this->grandTotalEE = 0;

        foreach ($contributions as $contribution) {
            $emp = $contribution->employee;
            $monthlyRate = (float) ($emp->monthly_rate ?? 0);
            $pi = is_string($contribution->hdmf_pi) ? json_decode($contribution->hdmf_pi, true) : $contribution->hdmf_pi;
            $primaryMid = $pi['pag_ibig_id_rtn'] ?? '';

            // --- 1. MANDATORY PAG-IBIG (F1) ---
            if ($contribution->hdmf_pi) {
                $eeShare = (float) ($pi['ee_share'] ?? 0);
                $minEE = 400.00;
                $surplus = max(0, $eeShare - $minEE);
                $moreThanMinEE = number_format($surplus, 2, '.', '');

                $runningTotal += $eeShare;
                $this->totalMin += $minEE;
                $this->totalMoreThanMin += $surplus;
                $this->grandTotalEE += $eeShare;

                $data[] = [
                    $primaryMid,
                    '',
                    'F1',
                    mb_strtoupper($emp->last_name ?? '', 'UTF-8'),
                    mb_strtoupper($emp->first_name ?? '', 'UTF-8'),
                    mb_strtoupper($emp->suffix ?? '', 'UTF-8'),
                    mb_strtoupper($emp->middle_initial ?? '', 'UTF-8'),
                    $pi['percov'] ?? '',
                    $monthlyRate,
                    (float) ($pi['er_share'] ?? 0),
                    $eeShare,
                    $minEE,
                    $moreThanMinEE,
                    $pi['remarks'] ?? '',
                ];
            }

            // --- 2. MP2 CONTRIBUTIONS (M2) ---
            if ($contribution->hdmf_mp2) {
                $mp2List = is_string($contribution->hdmf_mp2) ? json_decode($contribution->hdmf_mp2, true) : $contribution->hdmf_mp2;
                if (is_array($mp2List)) {
                    foreach ($mp2List as $mp2) {
                        $eeShareMp2 = (float) ($mp2['ee_share'] ?? 0);

                        $runningTotal += $eeShareMp2;
                        $this->totalM2 += $eeShareMp2;
                        $this->grandTotalEE += $eeShareMp2;

                        $rowMid = !empty($mp2['pag_ibig_id_rtn']) ? $mp2['pag_ibig_id_rtn'] : $primaryMid;

                        $data[] = [
                            $rowMid,
                            $mp2['account_number'] ?? '',
                            'M2',
                            mb_strtoupper($emp->last_name ?? '', 'UTF-8'),
                            mb_strtoupper($emp->first_name ?? '', 'UTF-8'),
                            mb_strtoupper($emp->suffix ?? '', 'UTF-8'),
                            mb_strtoupper($emp->middle_initial ?? '', 'UTF-8'),
                            $mp2['percov'] ?? '',
                            $monthlyRate,
                            0.00,
                            $eeShareMp2,
                            0.00,
                            'M2', 
                            $mp2['remarks'] ?? '',
                        ];
                    }
                }
            }
        }
        $this->headerInfo['total'] = $runningTotal;
        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastDataRow = $sheet->getHighestRow();
                $moneyFormat = '#,##0.00';

                $sheet->getColumnDimension('A')->setWidth(18);
                $sheet->getColumnDimension('B')->setWidth(18);
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(10);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(12);
                $sheet->getColumnDimension('I')->setWidth(16);
                $sheet->getColumnDimension('J')->setWidth(12);
                $sheet->getColumnDimension('K')->setWidth(12);
                $sheet->getColumnDimension('L')->setWidth(18);
                $sheet->getColumnDimension('M')->setWidth(22);
                $sheet->getColumnDimension('N')->setWidth(40);

                $sheet->getStyle("B{$this->startRow}:B{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A1', 'Employer ID No.');
                $sheet->setCellValueExplicit('B1', $this->headerInfo['employer_id'], DataType::TYPE_STRING);
                $sheet->setCellValue('A2', 'Employer/Business Name');
                $sheet->setCellValue('B2', $this->headerInfo['employer_name']);
                $sheet->setCellValue('A3', 'Employer/Business Address');
                $sheet->setCellValue('B3', $this->headerInfo['address']);
                $sheet->setCellValue('A4', 'Contact Number');
                $sheet->setCellValueExplicit('B4', $this->headerInfo['contact'], DataType::TYPE_STRING);
                $sheet->setCellValue('A5', 'Email Address');
                $sheet->setCellValue('B5', $this->headerInfo['email']);
                $sheet->setCellValue('A6', 'Total Remittance');
                $sheet->setCellValue('B6', $this->headerInfo['total']);
                $sheet->getStyle('B6')->getNumberFormat()->setFormatCode($moneyFormat);
                $sheet->getStyle('A1:A6')->getFont()->setBold(true);

                $sheet->mergeCells('A7:B7');
                $sheet->setCellValue('A7', 'Pag-IBIG IDENTIFICATION NO.');
                $sheet->setCellValue('A8', 'MID NO.');
                $sheet->setCellValue('B8', 'MP2 ACCOUNT NO.');
                $sheet->mergeCells('C7:C8');
                $sheet->setCellValue('C7', 'MEMBERSHIP PROGRAM');

                $cols = ['D' => 'LAST NAME', 'E' => 'FIRST NAME', 'F' => 'NAME EXTENSION', 'G' => 'MIDDLE NAME', 'H' => 'PERCOV'];
                foreach ($cols as $col => $text) {
                    $sheet->mergeCells("{$col}7:{$col}8");
                    $sheet->setCellValue("{$col}7", $text);
                }

                $sheet->mergeCells('I7:J7');
                $sheet->setCellValue('I7', 'For PAG-IBIG I only');
                $sheet->setCellValue('I8', 'MONTHLY COMPENSATION');
                $sheet->setCellValue('J8', 'ER SHARE');
                $sheet->mergeCells('K7:K8');
                $sheet->setCellValue('K7', 'EE SHARE');
                $sheet->mergeCells('L7:L8');
                $sheet->setCellValue('L7', 'HDMF MC (minimum)');
                $sheet->mergeCells('M7:M8');
                $sheet->setCellValue('M7', 'HDMF MC (more than minimum)');
                $sheet->mergeCells('N7:N8');
                $sheet->setCellValue('N7', 'REMARKS');

                $headerRange = 'A7:N8';
                $sheet->getStyle($headerRange)->getFont()->setBold(true);
                $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
                $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDDBDD');

                $sheet->getStyle("I{$this->startRow}:M{$lastDataRow}")->getNumberFormat()->setFormatCode($moneyFormat);
                $sheet->getStyle("I{$this->startRow}:N{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("A7:N{$lastDataRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $labelRow = $lastDataRow + 2;
                $sheet->setCellValue("J{$labelRow}", "TOTAL :");
                $sheet->setCellValue("K{$labelRow}", "M2");
                $sheet->setCellValue("L{$labelRow}", "MC (minimum)");
                $sheet->setCellValue("M{$labelRow}", "MC (more than minimum)");
                $sheet->getStyle("J{$labelRow}:M{$labelRow}")->getFont()->setBold(true);

                $valueRow = $labelRow + 1;
                // Use the PHP-calculated totals directly
                $sheet->setCellValue("K{$valueRow}", $this->totalM2);
                $sheet->setCellValue("L{$valueRow}", $this->totalMin);
                $sheet->setCellValue("M{$valueRow}", $this->totalMoreThanMin);
                $sheet->setCellValue("N{$valueRow}", $this->grandTotalEE);
                
                $sheet->getStyle("K{$valueRow}:N{$valueRow}")->getNumberFormat()->setFormatCode($moneyFormat);
                $sheet->getStyle("K{$valueRow}:N{$valueRow}")->getFont()->setBold(true);

                $combinedRow = $valueRow + 2;
                $sheet->mergeCells("J{$combinedRow}:K{$combinedRow}");
                $sheet->setCellValue("J{$combinedRow}", "MC (minimum + more than minimum)");
                // Summing the two numeric totals
                $sheet->setCellValue("L{$combinedRow}", $this->totalMin + $this->totalMoreThanMin);
                $sheet->getStyle("J{$combinedRow}:L{$combinedRow}")->getFont()->setBold(true);
                $sheet->getStyle("L{$combinedRow}")->getNumberFormat()->setFormatCode($moneyFormat);

                for ($i = $this->startRow; $i <= $lastDataRow; $i++) {
                    foreach (['A', 'B'] as $col) {
                        if ($val = $sheet->getCell($col . $i)->getValue()) {
                            $sheet->getCell($col . $i)->setValueExplicit($val, DataType::TYPE_STRING);
                            $sheet->getStyle($col . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        }
                    }
                }

                $sigRow = $combinedRow + 3;
                $sheet->setCellValue("A{$sigRow}", "Prepared by:");
                $sheet->setCellValue("D{$sigRow}", "Checked by:");
                $sheet->setCellValue("G{$sigRow}", "Funds Availability:");
                $nameRow = $sigRow + 2;
                $sheet->setCellValue("A{$nameRow}", strtoupper($this->signatories->prepared->name ?? ''));
                $sheet->setCellValue("D{$nameRow}", strtoupper($this->signatories->noted->name ?? ''));
                $sheet->setCellValue("G{$nameRow}", strtoupper($this->signatories->funds->name ?? ''));
                $sheet->getStyle("A{$nameRow}:G{$nameRow}")->getFont()->setBold(true);

                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LEGAL);
                $sheet->getPageSetup()->setFitToWidth(1);
            }
        ];
    }

    public function startCell(): string
    {
        return 'A' . $this->startRow;
    }
}