<?php

namespace App\Exports;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class ExportPayslip
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function generate()
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Add header image
        // $header = $section->addHeader();
        // $header->addImage(public_path('header.png'), ['width' => 950, 'height' => 150]);

        // Add footer image
        // $footer = $section->addFooter();
        // $footer->addImage(public_path('footer.png'), ['width' => 950, 'height' => 70]);

        // Content Title
        $section->addText('CERTIFICATE OF NET PAY', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Employee info
        $section->addText("Name: " . $this->data['full_name']);
        $section->addText("Position: " . $this->data['position']);
        $section->addText("Gross Monthly Income: Php " . number_format($this->data['gross_monthly_income'], 2));
        $section->addTextBreak(1);

        // Deductions header
        $section->addText("Less: Deductions");

        $contrib = $this->data['contributions'];
        $section->addText("HDMF-PI: Php " . number_format($contrib['hdmf_pi'] ?? 0, 2));
        $section->addText("HDMF-MPL: Php " . number_format($contrib['hdmf_mpl'] ?? 0, 2));
        $section->addText("HDMF-MP2: Php " . number_format($contrib['hdmf_mp2'] ?? 0, 2));
        $section->addText("HDMF-CL: Php " . number_format($contrib['hdmf_cl'] ?? 0, 2));
        $section->addText("DARECO: Php " . number_format($contrib['dareco'] ?? 0, 2));
        $section->addText("SSS: Php " . number_format($contrib['sss'] ?? 0, 2));
        $section->addText("EC: Php " . number_format($contrib['ec'] ?? 0, 2));
        $section->addText("WISP: Php " . number_format($contrib['wisp'] ?? 0, 2));

        $section->addText("Total Absent/Late: Php " . number_format($this->data['total_absent_late'] ?? 0, 2));
        $section->addText("Tax: Php " . number_format($this->data['tax'] ?? 0, 2));

        // Calculate total deductions and net pay
        $totalDeductions = array_sum(array_map(fn($v) => $v ?? 0, $contrib))
            + ($this->data['total_absent_late'] ?? 0)
            + ($this->data['tax'] ?? 0);

        $netPay = $this->data['gross_monthly_income'] - $totalDeductions;

        $section->addTextBreak(1);
        $section->addText("Total Deductions: Php " . number_format($totalDeductions, 2));
        $section->addText("Net Monthly Income: Php " . number_format($netPay, 2), ['bold' => true]);

        // Save Word to temp memory and return raw contents as string
        $tempFile = tempnam(sys_get_temp_dir(), 'phpword');
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        $contents = file_get_contents($tempFile);
        unlink($tempFile); // cleanup

        return $contents;
    }
}
