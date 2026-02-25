<?php

namespace App\Exports;

use App\Models\Contribution;
use App\Models\Assigned; // Added Assigned Model
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Carbon\Carbon;

class SssUpdated implements FromArray, WithEvents, WithCustomStartCell
{
    public $month;
    public $currentYear;
    public $signatories;
    protected $startRow = 4;

    public function __construct($month = null, $year = null)
    {
        $this->month = $month ?? Carbon::now()->month;
        $this->currentYear = $year ?? Carbon::now()->year;
        // Fetch signatories from your Assigned model
        $this->signatories = Assigned::with(['prepared', 'noted', 'funds', 'approved'])->latest()->first();
    }

    public function array(): array
    {
        $contributions = Contribution::with('employee')
            ->join('employees', 'contributions.employee_id', '=', 'employees.id')
            ->orderBy('employees.last_name', 'asc')
            ->select('contributions.*')
            ->get();

        $data = [];
        $totalMinCol = 0;
        $totalMoreCol = 0;
        $totalOverallSss = 0;

        foreach ($contributions as $contribution) {
            $sss = $this->decodeJson($contribution->sss);
            $ec = $this->decodeJson($contribution->ec);

            $sssAmount = isset($sss['amount']) ? (float) $sss['amount'] : 0;
            $ecAmount = isset($ec['amount']) ? (float) $ec['amount'] : 0;

            $totalSssContribution = $sssAmount + $ecAmount;
            $minContribution = 760.00;
            $moreThanMinimum = max(0, $totalSssContribution - $minContribution);

            $totalMinCol += $minContribution;
            $totalMoreCol += $moreThanMinimum;
            $totalOverallSss += $totalSssContribution;

            $middle = strtoupper(substr($contribution->employee->middle_initial ?? '', 0, 1));

            $data[] = [
                ucfirst(strtolower($contribution->employee->last_name ?? '')),
                ucfirst(strtolower($contribution->employee->first_name ?? '')),
                $middle ? $middle . '.' : '',
                trim(
                    ucfirst(strtolower($contribution->employee->first_name ?? '')) . ' ' .
                    ($middle ? $middle . '. ' : '') .
                    ucfirst(strtolower($contribution->employee->last_name ?? '')) .
                    ($contribution->employee->suffix ? ' ' . ucfirst(strtolower($contribution->employee->suffix)) . '.' : '')
                ),
                $minContribution,
                $moreThanMinimum ?: null,
                $totalSssContribution,
                $sss['remarks'] ?? '',
            ];
        }

        $data[] = [
            '',
            '',
            '',
            'TOTAL',
            $totalMinCol,
            $totalMoreCol,
            $totalOverallSss,
            '',
        ];

        return $data;
    }

    protected function decodeJson($value)
    {
        if (is_array($value))
            return $value;
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_string($decoded))
                $decoded = json_decode($decoded, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
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

                // Setup Page
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LEGAL);
                $sheet->getPageMargins()->setTop(0.9843);
                $sheet->getPageMargins()->setBottom(0.2362);
                $sheet->getPageMargins()->setLeft(0.5118);
                $sheet->getPageMargins()->setRight(0.5118);
                $sheet->getPageSetup()->setScale(72);

                $columns = ['A' => 18, 'B' => 18, 'C' => 8, 'D' => 36, 'E' => 32, 'F' => 32, 'G' => 30, 'H' => 16];
                foreach ($columns as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // Main Header
                $monthName = Carbon::create()->month($this->month)->format('F');
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'UPDATED AS OF ' . strtoupper($monthName) . ' ' . $this->currentYear);
                $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:H1')->getFont()->setBold(true);

                // Table Headers
                $sheet->setCellValue('A2', 'LAST NAME');
                $sheet->setCellValue('B2', 'FIRST NAME');
                $sheet->setCellValue('C2', 'M.I.');
                $sheet->setCellValue('D2', 'NAME OF INCUMBENT (FULL NAME)');
                $sheet->setCellValue('E2', 'SSS CONTRIBUTION');
                $sheet->setCellValue('G2', 'TOTAL SSS CONTRIBUTION');
                $sheet->setCellValue('H2', '');
                $sheet->setCellValue('E3', 'SS CON + EC CON (MINIMUM)');
                $sheet->setCellValue('F3', 'SS CON (MORE THAN MINIMUM)');

                $sheet->mergeCells('E2:F2');
                $sheet->mergeCells('A2:A3');
                $sheet->mergeCells('B2:B3');
                $sheet->mergeCells('C2:C3');
                $sheet->mergeCells('D2:D3');
                $sheet->mergeCells('G2:G3');
                $sheet->mergeCells('H2:H3');

                $sheet->getStyle("A2:G3")->getFont()->setBold(true);
                $sheet->getStyle("A2:G3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                $lastRow = $sheet->getHighestRow();

                // Borders for Table only (A to G)
                $sheet->getStyle("A2:G{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("E{$this->startRow}:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Total Row Style
                $totalRow = $lastRow;
                $sheet->getStyle("D{$totalRow}:G{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF2CC']],
                ]);

                foreach (['E', 'F', 'G'] as $col) {
                    $sheet->getStyle("{$col}{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                    $range = "{$col}{$this->startRow}:{$col}" . ($totalRow - 1);
                    $sheet->getStyle($range)->getNumberFormat()->setFormatCode('#,##0.00');
                }

                $signatoryStartRow = $totalRow + 4;

                $sheet->setCellValue("A{$signatoryStartRow}", "Prepared by:");
                $sheet->setCellValue("D{$signatoryStartRow}", "Checked by:");
                $sheet->setCellValue("G{$signatoryStartRow}", "Funds Availability:");

                $nameRow = $signatoryStartRow + 2;
                $sheet->setCellValue("A{$nameRow}", strtoupper($this->signatories->prepared->name ?? ''));
                $sheet->setCellValue("D{$nameRow}", strtoupper($this->signatories->noted->name ?? ''));
                $sheet->setCellValue("G{$nameRow}", strtoupper($this->signatories->funds->name ?? ''));

                $sheet->getStyle("A{$nameRow}:G{$nameRow}")->getFont()->setBold(true);

                $sheet->setCellValue("A" . ($nameRow + 1), "Payroll Clerk");
                $sheet->setCellValue("D" . ($nameRow + 1), "OIC, HRMU");
                $sheet->setCellValue("G" . ($nameRow + 1), "OIC, Accounting Unit");

                // Final Page Setup
                $sheet->getPageSetup()->setPrintArea("A1:H" . ($nameRow + 2));
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            }
        ];
    }
}