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



class HdmfMp2 implements FromArray, WithEvents, WithCustomStartCell
{
    public $signatories = [];
    public $total_mp2_amortization = 0;

    public function __construct()
    {
        $this->signatories = Assigned::with(['prepared', 'noted', 'funds', 'approved'])->latest()->first();
    }
    protected $headerInfo = [
        'employer_id' => '210457140007',
        'employer_name' => 'BUREAU OF FISHERIES AND AQUATIC RESOURCES XI - CONTRACT OF SERVICE',
        'address' => 'RAMON MAGSAYSAY AVENUE, DAVAO CITY',
    ];
    protected $startRow = 5;
    public function array(): array
    {
        $data = Contribution::with('employee')
            ->whereNotNull('hdmf_mp2')
            ->get()
            ->flatMap(function ($contribution) {
                $rows = [];
                $mp2Raw = $contribution->hdmf_mp2 ?? '';
                if (is_string($mp2Raw)) {
                    $decoded = json_decode($mp2Raw, true);

                    if (is_string($decoded)) {
                        $decoded = json_decode($decoded, true);
                    }
                    if (!is_array($decoded)) {
                        return [];
                    }
                    foreach ($decoded as $entry) {
                        if (!is_array($entry)) {
                            continue;
                        }
                        $eeShare = is_numeric($entry['ee_share'] ?? null) ? (float) $entry['ee_share'] : 0;
                        $this->total_mp2_amortization += $eeShare;
                        $rows[] = [
                            $entry['pag_big_id_rtn'] ?? '',
                            $entry['account_number'] ?? '',
                            $entry['mem_program'] ?? '',
                            strtoupper($contribution->employee->last_name ?? ''),
                            strtoupper($contribution->employee->first_name ?? ''),
                            strtoupper($contribution->employee->suffix ?? ''),
                            strtoupper($contribution->employee->middle_initial ?? ''),
                            $entry['percov'] ?? '',
                            is_numeric($entry['ee_share'] ?? null) ? number_format((float) $entry['ee_share'], 2) : '',
                            is_numeric($entry['er_share'] ?? null) ? number_format((float) $entry['er_share'], 2) : '',
                            $entry['remarks'] ?? '',
                        ];
                    }
                }
                return $rows;
            })->toArray();

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

                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LEGAL);
                $sheet->getPageMargins()->setTop(0.9843);
                $sheet->getPageMargins()->setBottom(0.2362);
                $sheet->getPageMargins()->setLeft(0.5118);
                $sheet->getPageMargins()->setRight(0.5118);
                $sheet->getPageSetup()->setScale(72);

                $sheet->getColumnDimension('A')->setWidth(18);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(30);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(12);
                $sheet->getColumnDimension('H')->setWidth(12);
                $sheet->getColumnDimension('I')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(15);
                $sheet->getColumnDimension('J')->setWidth(15);
                $sheet->getColumnDimension('K')->setWidth(15);

                $sheet->setCellValue('A1', 'Employer ID');
                $sheet->setCellValueExplicit('B1', $this->headerInfo['employer_id'], DataType::TYPE_STRING);
                $sheet->setCellValue('K1', 'HQP-SLF-017');
                $sheet->setCellValue('A2', 'Employer Name');
                $sheet->setCellValue('B2', $this->headerInfo['employer_name']);
                $sheet->setCellValue('A3', 'Address');
                $sheet->setCellValue('B3', $this->headerInfo['address']);
                $sheet->getStyle('A1')->getFont()->setBold(true);
                $sheet->getStyle('B1')->getFont()->setBold(true);
                $sheet->getStyle('I1')->getFont()->setBold(true);
                $sheet->getStyle('A2')->getFont()->setBold(true);
                $sheet->getStyle('B2')->getFont()->setBold(true);
                $sheet->getStyle('A3')->getFont()->setBold(true);
                $sheet->getStyle('B3')->getFont()->setBold(true);



                $headerRow = $this->startRow - 1;
                $headings = [
                    'A' => 'Pag-IBIG ID/RTN',
                    'B' => 'ACCOUNT NO',
                    'C' => 'MEMBERSHIP PROGRAM',
                    'D' => 'LAST NAME',
                    'E' => 'FIRST NAME',
                    'F' => 'NAME EXTENSION',
                    'G' => 'MIDDLE NAME',
                    'H' => 'PERCOV',
                    'I' => 'EE SHARE',
                    'J' => 'ER SHARE',
                    'K' => 'REMARKS',
                ];


                foreach ($headings as $col => $text) {
                    $sheet->setCellValue("{$col}{$headerRow}", $text);
                }

                $sheet->getStyle("A{$headerRow}:K{$headerRow}")->getFont()->setBold(true);
                $sheet->getRowDimension($headerRow)->setRowHeight(35);
                $sheet->getStyle("A{$headerRow}:K{$headerRow}")->getAlignment()->setWrapText(true);

                $sheet->getStyle("A{$headerRow}:K{$headerRow}")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('DDDBDD');

                $sheet->getStyle("A{$headerRow}:K{$headerRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_BOTTOM);

                $sheet->getStyle("A{$headerRow}:K{$headerRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF000000'));



                foreach (range($this->startRow, $sheet->getHighestRow()) as $row) {
                    $sheet->getCell('A' . $row)
                        ->setValueExplicit(
                            $sheet->getCell('A' . $row)->getValue(),
                            DataType::TYPE_STRING
                        );
                }

                $lastDataRow = $sheet->getHighestRow();
                // $sheet->getStyle("A{$this->startRow}:F{$lastDataRow}")->getFont()->setBold(true);
                $lastDataRow = $sheet->getHighestRow();
                $dataRange = "A{$this->startRow}:K{$lastDataRow}";
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF000000'));
                $sheet->getStyle("H{$this->startRow}:H{$lastDataRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("E{$this->startRow}:E{$lastDataRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("G{$this->startRow}:G{$lastDataRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);


                $totalLabelRow = $lastDataRow + 4;
                $totalAmountRow = $totalLabelRow;
                $sheet->setCellValue("A{$totalLabelRow}", "TOTAL MPL AMORTIZATION");
                $sheet->getStyle("A{$totalLabelRow}")->getFont()->setBold(true);
                $sheet->setCellValue("H{$totalAmountRow}", number_format($this->total_mp2_amortization, 2));
                $sheet->getStyle("H{$totalAmountRow}")
                    ->getNumberFormat()
                    ->setFormatCode('₱#,##0.00');
                $sheet->getStyle("H{$totalAmountRow}")->getFont()->setBold(true);
                $sheet->getStyle("H{$totalAmountRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $signatoryStartRow = $totalLabelRow + 3;




                $sheet->setCellValue("A{$signatoryStartRow}", "Prepared by:");
                $sheet->setCellValue("D{$signatoryStartRow}", "Checked by:");
                $sheet->setCellValue("G{$signatoryStartRow}", "Funds Availability:");

                $nameRow = $signatoryStartRow + 2;
                $sheet->setCellValue("A{$nameRow}", strtoupper($this->signatories->prepared->name ?? ''));
                $sheet->setCellValue("D{$nameRow}", strtoupper($this->signatories->noted->name ?? ''));
                $sheet->setCellValue("G{$nameRow}", strtoupper($this->signatories->funds->name ?? ''));
                $sheet->getStyle("A{$nameRow}")->getFont()->setBold(true);
                $sheet->getStyle("D{$nameRow}")->getFont()->setBold(true);
                $sheet->getStyle("G{$nameRow}")->getFont()->setBold(true);

                $sheet->setCellValue("A" . ($signatoryStartRow + 3), "Payroll Clerk");
                $sheet->setCellValue("D" . ($signatoryStartRow + 3), "OIC, HRMU");
                $sheet->setCellValue("G" . ($signatoryStartRow + 3), "OIC, Accounting Unit");


                $sheet->getPageSetup()->setPrintArea('A1:I' . $sheet->getHighestRow());
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            }
        ];
    }
}
