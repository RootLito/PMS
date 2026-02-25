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
        'total' => '', 
    ];

    // Table starts after the 6 header rows + 1 gap
    protected $startRow = 9;

    public function __construct()
    {
        $this->signatories = Assigned::with(['prepared', 'noted', 'funds', 'approved'])->latest()->first();
    }

    public function array(): array
    {
        $contributions = Contribution::with('employee')
            ->whereNotNull('hdmf_pi')
            ->join('employees', 'contributions.employee_id', '=', 'employees.id')
            ->orderBy('employees.last_name', 'asc')
            ->select('contributions.*')
            ->get();

        $data = [];
        $runningTotal = 0;

        foreach ($contributions as $contribution) {
            $piRaw = $contribution->hdmf_pi;
            $piDecoded = is_string($piRaw) ? json_decode($piRaw, true) : $piRaw;
            
            $eeShare = (float)($piDecoded['ee_share'] ?? 0);
            $minEE = 100.00; 
            $moreThanMinEE = max(0, $eeShare - $minEE);
            
            $runningTotal += $eeShare;

            $data[] = [
                $piDecoded['pag_ibig_id_rtn'] ?? '', 
                $piDecoded['app_no'] ?? '',          
                $piDecoded['mem_program'] ?? '',     
                mb_strtoupper($contribution->employee->last_name ?? '', 'UTF-8'),
                mb_strtoupper($contribution->employee->first_name ?? '', 'UTF-8'),
                mb_strtoupper($contribution->employee->suffix ?? '', 'UTF-8'),
                mb_strtoupper($contribution->employee->middle_initial ?? '', 'UTF-8'),
                $piDecoded['percov'] ?? '',          
                $piDecoded['monthly_comp'] ?? '',    
                $piDecoded['er_share'] ?? '',        
                $minEE,                              
                $moreThanMinEE ?: null,              
                $eeShare,                            
                $piDecoded['remarks'] ?? '',         
            ];
        }

        $this->headerInfo['total'] = $runningTotal;

        return $data;
    }

    public function startCell(): string
    {
        return 'A' . $this->startRow;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // --- THE 6 HEADER ROWS ---
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
                $sheet->getStyle('B6')->getNumberFormat()->setFormatCode('#,##0.00');

                // Bold labels A1 to A6
                $sheet->getStyle('A1:A6')->getFont()->setBold(true);

                // --- TABLE HEADERS (Rows 7 & 8) ---
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

                $sheet->mergeCells('K7:M7');
                $sheet->setCellValue('K7', 'EE SHARE');
                $sheet->setCellValue('K8', 'HDMF MC (minimum)');
                $sheet->setCellValue('L8', 'HDMF MC (more than minimum)');
                $sheet->setCellValue('M8', 'TOTAL');

                $sheet->mergeCells('N7:N8');
                $sheet->setCellValue('N7', 'REMARKS');

                // Header Styling
                $headerRange = 'A7:N8';
                $sheet->getStyle($headerRange)->getFont()->setBold(true);
                $sheet->getStyle($headerRange)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);
                
                $sheet->getStyle($headerRange)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('DDDBDD');

                // Apply Borders to data
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A7:N{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // --- FIX FOR SCIENTIFIC NOTATION (MID NO.) HERE ---
                for ($i = $this->startRow; $i <= $lastRow; $i++) {
                    $midNo = $sheet->getCell('A' . $i)->getValue();
                    if ($midNo) {
                        $sheet->getCell('A' . $i)->setValueExplicit($midNo, DataType::TYPE_STRING);
                    }
                }

                // --- SIGNATORIES (3 Row Gap) ---
                $sigRow = $lastRow + 4;
                $sheet->setCellValue("A{$sigRow}", "Prepared by:");
                $sheet->setCellValue("D{$sigRow}", "Checked by:");
                $sheet->setCellValue("G{$sigRow}", "Funds Availability:");

                $nameRow = $sigRow + 2;
                $sheet->setCellValue("A{$nameRow}", strtoupper($this->signatories->prepared->name ?? ''));
                $sheet->setCellValue("D{$nameRow}", strtoupper($this->signatories->noted->name ?? ''));
                $sheet->setCellValue("G{$nameRow}", strtoupper($this->signatories->funds->name ?? ''));
                $sheet->getStyle("A{$nameRow}:G{$nameRow}")->getFont()->setBold(true);

                $sheet->setCellValue("A" . ($nameRow + 1), "Payroll Clerk");
                $sheet->setCellValue("D" . ($nameRow + 1), "OIC, HRMU");
                $sheet->setCellValue("G" . ($nameRow + 1), "OIC, Accounting Unit");

                // Page Setup
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LEGAL);
                $sheet->getPageSetup()->setFitToWidth(1);
            }
        ];
    }
}