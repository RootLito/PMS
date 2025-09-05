<?php

namespace App\Exports;

use App\Models\Contribution;
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

class SssEcWisp implements FromArray, WithEvents, WithCustomStartCell
{
    public $month;
    public $currentYear;
    public $signatories = [];
    public $total_mpl_amortization = 0;
    protected $startRow = 3;

    public function __construct($month = null, $year = null)
    {
        // Set dynamic month/year or default to current
        $this->month = $month ?? Carbon::now()->month;
        $this->currentYear = $year ?? Carbon::now()->year;
    }

    public function array(): array
    {
        $contributions = Contribution::with('employee')
            ->join('employees', 'contributions.employee_id', '=', 'employees.id')
            ->orderBy('employees.last_name', 'asc')
            ->select('contributions.*')
            ->get();

        $data = [];
        $totalSss = 0;
        $totalEc = 0;
        $totalWisp = 0;
        $totalOverall = 0;

        foreach ($contributions as $contribution) {
            $sss = $this->decodeJson($contribution->sss);
            $ec = $this->decodeJson($contribution->ec);
            $wisp = $this->decodeJson($contribution->wisp);

            $sssAmount = isset($sss['amount']) ? (float) $sss['amount'] : 0;
            $ecAmount = isset($ec['amount']) ? (float) $ec['amount'] : 0;
            $wispAmount = isset($wisp['amount']) ? (float) $wisp['amount'] : 0;
            $difference = isset($sss['difference']) ? (float) $sss['difference'] : 0;

            $total = $sssAmount + $ecAmount + $wispAmount;

            $totalSss += $sssAmount;
            $totalEc += $ecAmount;
            $totalWisp += $wispAmount;
            $totalOverall += $total;

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
                $sssAmount ?: null,
                $ecAmount ?: null,
                $wispAmount ?: null,
                $total ?: null,
                $difference ?: null,
                $sss['remarks'] ?? 'None',
                $contribution->employee->designation ?? 'None',
            ];
        }

        // Total Row
        $data[] = [
            '',
            '',
            '',
            'TOTAL',
            $totalSss ? number_format($totalSss, 2, '.', '') : null,
            $totalEc ? number_format($totalEc, 2, '.', '') : null,
            $totalWisp ? number_format($totalWisp, 2, '.', '') : null,
            $totalOverall ? number_format($totalOverall, 2, '.', '') : null,
            '',
            '',
            ''
        ];

        return $data;
    }

    protected function decodeJson($value)
    {
        if (is_array($value)) return $value;

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
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

                // Page setup
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LEGAL);
                $sheet->getPageMargins()->setTop(0.9843);
                $sheet->getPageMargins()->setBottom(0.2362);
                $sheet->getPageMargins()->setLeft(0.5118);
                $sheet->getPageMargins()->setRight(0.5118);
                $sheet->getPageSetup()->setScale(72);

                // Column widths
                $columns = [
                    'A' => 18,
                    'B' => 18,
                    'C' => 8,
                    'D' => 32,
                    'E' => 12,
                    'F' => 12,
                    'G' => 12,
                    'H' => 15,
                    'I' => 15,
                    'J' => 25,
                    'K' => 30
                ];

                foreach ($columns as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // Dynamic Month and Year Heading
                $monthName = Carbon::create()->month($this->month)->format('F');
                $sheet->mergeCells('E1:H1');
                $sheet->setCellValue('E1', 'UPDATED AS OF ' . strtoupper($monthName) . ' ' . $this->currentYear);
                $sheet->getStyle('E1:H1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E1:H1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('E1:H1')->getFont()->setBold(true);

                // Header Row
                $headerRow = 2;
                $headings = [
                    'A' => 'LAST NAME',
                    'B' => 'FIRST NAME',
                    'C' => 'M.I.',
                    'D' => 'FULL NAME',
                    'E' => 'SSS',
                    'F' => 'EC',
                    'G' => 'WISP',
                    'H' => 'TOTAL',
                    'I' => 'DIFFERENCE',
                    'J' => 'REMARKS',
                    'K' => 'DESIGNATION',
                ];

                foreach ($headings as $col => $text) {
                    $sheet->setCellValue("{$col}{$headerRow}", $text);
                }

                $sheet->getStyle("A{$headerRow}:K{$headerRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$headerRow}:I{$headerRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $lastRow = $sheet->getHighestRow();

                // Borders and alignments
                $sheet->getStyle("A{$this->startRow}:I{$lastRow}")
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->setColor(new Color('FF000000'));

                $sheet->getStyle("E{$this->startRow}:H{$lastRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle("I{$this->startRow}:I{$lastRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getPageSetup()->setPrintArea("A1:I{$lastRow}");
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);

                // Highlight total row
                $totalRow = $lastRow;
                $sheet->getStyle("D{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF2CC'],
                    ],
                ]);

                foreach (['E', 'F', 'G', 'H'] as $col) {
                    $sheet->getStyle("{$col}{$totalRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_RIGHT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFF2CC'],
                        ],
                        'numberFormat' => [
                            'formatCode' => '#,##0.00',
                        ],
                    ]);
                }

                // Format number columns
                $dataLastRow = $lastRow - 1;
                foreach (['E', 'F', 'G', 'H', 'I'] as $col) {
                    $range = "{$col}{$this->startRow}:{$col}{$dataLastRow}";
                    $sheet->getStyle($range)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                }
            }
        ];
    }
}
