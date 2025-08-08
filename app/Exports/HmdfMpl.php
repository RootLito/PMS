<?php

namespace App\Exports;

use App\Models\Contribution;
use App\Models\Assigned;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

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
        $data = Contribution::with('employee')->get()->map(function ($contribution) {
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
                $contribution->employee->last_name ?? '',
                $contribution->employee->first_name ?? '',
                $contribution->employee->suffix ?? '',
                $contribution->employee->middle_initial ?? '',
                $mplDecoded['loan_type'] ?? '',
                $mplDecoded['amount'] ?? '',
                $mplDecoded['remarks'] ?? '',
                $mplDecoded['start_te'] ?? '',
                $mplDecoded['end_te'] ?? '',
                '', // blank column L
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
                ];
                foreach ($headings as $col => $text) {
                    $sheet->setCellValue("{$col}{$headerRow}", $text);
                }
                $sheet->getStyle("A{$headerRow}:M{$headerRow}")->getFont()->setBold(true);
                foreach (range($this->startRow, $sheet->getHighestRow()) as $row) {
                    $sheet->getCell('A' . $row)
                        ->setValueExplicit(
                            $sheet->getCell('A' . $row)->getValue(),
                            DataType::TYPE_STRING
                        );
                }
                $lastDataRow = $sheet->getHighestRow();
                $sheet->getStyle("A{$this->startRow}:F{$lastDataRow}")->getFont()->setBold(true);
                $totalLabelRow = $lastDataRow + 4;
                $totalAmountRow = $totalLabelRow;
                $sheet->setCellValue("A{$totalLabelRow}", "TOTAL MPL AMORTIZATION");
                $sheet->getStyle("A{$totalLabelRow}")->getFont()->setBold(true);
                $sheet->setCellValue("H{$totalAmountRow}", number_format($this->total_mpl_amortization, 2));
                $sheet->getStyle("H{$totalAmountRow}")->getFont()->setBold(true);
                $sheet->getStyle("H{$totalAmountRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
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
            }
        ];
    }
}
