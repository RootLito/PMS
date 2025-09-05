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
class HmdfMpl implements FromArray, WithEvents, WithCustomStartCell
{

    public $signatories = [];
    public $total_mpl_amortization = 0;

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
            ->whereNotNull('hdmf_mpl')
            ->get()
            ->map(function ($contribution) {
                $mplRaw = $contribution->hdmf_mpl ?? '';
                $mplDecoded = [];
                if (is_string($mplRaw)) {
                    $firstDecode = json_decode($mplRaw, true);
                    $mplDecoded = is_string($firstDecode) ? json_decode($firstDecode, true) : $firstDecode;
                    if (!is_array($mplDecoded)) {
                        $mplDecoded = [];
                    }
                } elseif (is_array($mplRaw)) {
                    $mplDecoded = $mplRaw;
                }
                $amount = isset($mplDecoded['amount']) ? (float) $mplDecoded['amount'] : 0;
                $this->total_mpl_amortization += $amount;

                return [
                    $mplDecoded['pag_ibig_id_rtn'] ?? '',
                    $mplDecoded['app_no'] ?? '',
                    // strtoupper($contribution->employee->last_name ?? ''),
                    // strtoupper($contribution->employee->first_name ?? ''),
                    // strtoupper($contribution->employee->suffix ?? ''),
                    // strtoupper($contribution->employee->middle_initial ?? ''),
                    mb_strtoupper($contribution->employee->last_name ?? '', 'UTF-8'),
                    mb_strtoupper($contribution->employee->first_name ?? '', 'UTF-8'),
                    mb_strtoupper($contribution->employee->suffix ?? '', 'UTF-8'),
                    mb_strtoupper($contribution->employee->middle_initial ?? '', 'UTF-8'),
                    $mplDecoded['loan_type'] ?? '',
                    isset($mplDecoded['amount']) ? number_format($mplDecoded['amount'], 2) : '',
                    '',
                    $mplDecoded['start_te'] ?? '',
                    $mplDecoded['end_te'] ?? '',
                    '',
                    $mplDecoded['status'] ?? '',
                    $mplDecoded['notes'] ?? '',
                ];
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
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(18);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(12);
                $sheet->getColumnDimension('H')->setWidth(12);
                $sheet->getColumnDimension('I')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(15);
                $sheet->getColumnDimension('J')->setWidth(15);
                $sheet->getColumnDimension('K')->setWidth(15);
                $sheet->getColumnDimension('M')->setWidth(45);

                $sheet->setCellValue('A1', 'Employer ID');
                $sheet->setCellValueExplicit('B1', $this->headerInfo['employer_id'], DataType::TYPE_STRING);
                $sheet->setCellValue('I1', 'HQP-SLF-017');
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
                    'B' => 'APPLICATION NO/AGREEMENT NO',
                    'C' => 'LAST NAME',
                    'D' => 'FIRST NAME',
                    'E' => 'NAME EXTENSION',
                    'F' => 'MIDDLE NAME',
                    'G' => 'LOAN TYPE',
                    'H' => 'AMOUNT',
                    'I' => 'REMARKS',
                    'J' => 'start_term',
                    'K' => 'end_term',
                    'L' => '',
                    'M' => 'REMARKS',
                    'N' => '',
                ];


                foreach ($headings as $col => $text) {
                    $sheet->setCellValue("{$col}{$headerRow}", $text);
                }

                $sheet->getStyle("A{$headerRow}:I{$headerRow}")->getFont()->setBold(true);
                $sheet->getRowDimension($headerRow)->setRowHeight(35);
                $sheet->getStyle("A{$headerRow}:I{$headerRow}")->getAlignment()->setWrapText(true);

                $sheet->getStyle("A{$headerRow}:I{$headerRow}")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('DDDBDD');

                $sheet->getStyle("A{$headerRow}:I{$headerRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_BOTTOM);

                $sheet->getStyle("A{$headerRow}:I{$headerRow}")
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
                $sheet->getStyle("A{$this->startRow}:F{$lastDataRow}")->getFont()->setBold(true);
                $lastDataRow = $sheet->getHighestRow();
                $dataRange = "A{$this->startRow}:I{$lastDataRow}";
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
                $sheet->setCellValue("H{$totalAmountRow}", number_format($this->total_mpl_amortization, 2));
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
