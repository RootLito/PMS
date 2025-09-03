<?php

namespace App\Exports;

use App\Models\Contribution;
use App\Models\Assigned;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class SssEcWisp implements FromArray, WithEvents, WithCustomStartCell
{
    public $signatories = [];
    public $total_mpl_amortization = 0;
    protected $startRow = 3;
    public function array(): array
    {
        return Contribution::with('employee')
            ->join('employees', 'contributions.employee_id', '=', 'employees.id') 
            ->orderBy('employees.last_name', 'asc') 
            ->select('contributions.*')
            ->get()
            ->map(function ($contribution) {
                $sss = $this->decodeJson($contribution->sss);
                $ec = $this->decodeJson($contribution->ec);
                $wisp = $this->decodeJson($contribution->wisp);

                $sssAmount = isset($sss['amount']) ? (float) $sss['amount'] : 0;
                $ecAmount = isset($ec['amount']) ? (float) $ec['amount'] : 0;
                $wispAmount = isset($wisp['amount']) ? (float) $wisp['amount'] : 0;
                $difference = isset($sss['difference']) ? (float) $sss['difference'] : 0;

                $total = $sssAmount + $ecAmount + $wispAmount;

                return [
                    ucfirst(strtolower($contribution->employee->last_name ?? '')),
                    ucfirst(strtolower($contribution->employee->first_name ?? '')),
                    ($middle = strtoupper(substr($contribution->employee->middle_initial ?? '', 0, 1))) ? $middle . '.' : '',
                    trim(
                        ucfirst(strtolower($contribution->employee->first_name ?? '')) . ' ' .
                        (strtoupper(substr($contribution->employee->middle_initial ?? '', 0, 1)) ? strtoupper(substr($contribution->employee->middle_initial ?? '', 0, 1)) . '. ' : '') .
                        ucfirst(strtolower($contribution->employee->last_name ?? '')) .
                        ($contribution->employee->suffix ? ' ' . ucfirst(strtolower($contribution->employee->suffix)) . '.' : '')
                    ),
                    $sssAmount ? number_format($sssAmount, 2) : '',
                    $ecAmount ? number_format($ecAmount, 2) : '',
                    $wispAmount ? number_format($wispAmount, 2) : '',
                    $total ? number_format($total, 2) : '',
                    $difference ? number_format($difference, 2) : '',
                    $sss['remarks'] ?? '',
                    $contribution->employee->designation ?? '',
                ];
            })->toArray();
    }

    protected function decodeJson($value)
    {
        if (is_array($value))
            return $value;

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

                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LEGAL);
                $sheet->getPageMargins()->setTop(0.9843);
                $sheet->getPageMargins()->setBottom(0.2362);
                $sheet->getPageMargins()->setLeft(0.5118);
                $sheet->getPageMargins()->setRight(0.5118);
                $sheet->getPageSetup()->setScale(72);
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
                $sheet->mergeCells('E1:H1');
                $sheet->setCellValue('E1', 'UPDATED AS OF JANUARY 2025');
                $sheet->getStyle('E1:H1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E1:H1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('E1:H1')->getFont()->setBold(true);
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
                $sheet->getStyle("A{$this->startRow}:I{$lastRow}")
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('FF000000'));
                $sheet->getStyle("E{$this->startRow}:H{$lastRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("I{$this->startRow}:I{$lastRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getPageSetup()->setPrintArea("A1:I{$lastRow}");
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            }
        ];
    }
}
