<?php

namespace App\Services;

use App\Models\SheetConfig;
use Carbon\Carbon;
use Elibyy\TCPDF\Facades\TCPDF;

class SheetService
{
    protected $width;

    protected $inputs;

    public function generate($inputs)
    {
        $this->inputs = $inputs;

        $pdf = new TCPDF();
        $pdf::SetTitle($inputs['sheet']);
        $pdf::AddPage('', 'LETTER');

        // set cell padding
        $pdf::setCellPaddings(1, 1, 1, 1);

        // set cell margins
        $pdf::setCellMargins(1, 1, 1, 1);

        // set color for background
        $pdf::setFillColor(255, 255, 127);

        // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

        // set some text for example
        $txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';



        $this->width = 44.5;
        $count = 20;

        $sheetConfigs = SheetConfig::query()
            ->with(['sheet:id,name','medication:id,name','unit:id,name'])
            ->whereRelation('sheet', 'name', '=', $inputs['sheet'])
            ->get();

        $sheetConfigByCoordinates = [];
        foreach ($sheetConfigs as $sheetConfig) {
            $sheetConfigByCoordinates[$sheetConfig->row][$sheetConfig->column] = $sheetConfig;
        }

        for ($i = 0; $i < $count; $i++) {
            // Multicell test
            $this->writeRow($pdf, $i, $sheetConfigByCoordinates);
            $pdf::Ln();
        }

        $pdf::Ln(4);

        $pdf::Output($inputs['sheet'] . '.pdf');
    }

    protected function writeRow($pdf, $rowNum, $sheetConfigByCoordinates)
    {
        $pdf::setCellMargins(0, 1, 1, 1);
        $this->writeLabel($pdf, $sheetConfigByCoordinates[$rowNum][1]);
        $pdf::setCellMargins(6.5, 1, 1, 1);
        $this->writeLabel($pdf, $sheetConfigByCoordinates[$rowNum][2]);
        $this->writeLabel($pdf, $sheetConfigByCoordinates[$rowNum][3]);
        $this->writeLabel($pdf, $sheetConfigByCoordinates[$rowNum][4]);
        $pdf::setCellMargins(0, 1, 1, 1);

        $pdf::Ln();

        $this->writePrepAndExpDates($pdf, $sheetConfigByCoordinates[$rowNum][1]);
        $pdf::setCellMargins(6.5, 1, 1, 1);
        $this->writePrepAndExpDates($pdf, $sheetConfigByCoordinates[$rowNum][2]);
        $this->writePrepAndExpDates($pdf, $sheetConfigByCoordinates[$rowNum][3]);
        $this->writePrepAndExpDates($pdf, $sheetConfigByCoordinates[$rowNum][4]);
        $pdf::setCellMargins(0, 1, 1, 1);

        $pdf::Ln();

        $this->writeInitialAndExpTime($pdf, $sheetConfigByCoordinates[$rowNum][1]);
        $pdf::setCellMargins(6.5, 1, 1, 1);
        $this->writeInitialAndExpTime($pdf, $sheetConfigByCoordinates[$rowNum][2]);
        $this->writeInitialAndExpTime($pdf, $sheetConfigByCoordinates[$rowNum][3]);
        $this->writeInitialAndExpTime($pdf, $sheetConfigByCoordinates[$rowNum][4]);
        $pdf::setCellMargins(0, 1, 1, 1);
    }

    protected function writeLabel($pdf, $labelConfig)
    {
        $pdf::setFontSize(10);
        $nameAndDosage = $labelConfig->medication->name
            . ' ' . $labelConfig->dosage
            . ' <small>' . $labelConfig->unit->name . '</small>';

        $pdf::writeHTMLCell($this->width, 5, $pdf::getX(), $pdf::getY(), $nameAndDosage, 'LTR', 0, true, true, 'C', '', true);
    }

    protected function writePrepAndExpDates($pdf, $labelConfig)
    {
        $pdf::setFontSize(6.5);
        $prepAndExpDatesString = 'Prep Date '
            . '<u>' . Carbon::parse($this->inputs['prep_date'])->format('m/d/y') . '</u>'
            . ' &nbsp;&nbsp;&nbsp; Exp Date '
            . '<u>' . Carbon::parse($this->inputs['exp_date'])->format('m/d/y') . '</u>';
        $pdf::writeHTMLCell($this->width, 1, $pdf::getX(), $pdf::getY(), $prepAndExpDatesString, 'LR', 0, true, true, 'C', '', true);
    }

    protected function writeInitialAndExpTime($pdf, $labelConfig)
    {
        $pdf::setFontSize(6.5);
        $initialAndExpTimeString = (strlen($this->inputs['name']) > 5 ? '' : 'Initial ')
            . '<u>' . $this->inputs['name'] . '</u>'
            . ' &nbsp;&nbsp;&nbsp; Exp Time '
            . '<u>' . Carbon::parse($this->inputs['exp_time'])->format('m/d/y') . '</u>';
        $pdf::writeHTMLCell($this->width, 3.75, $pdf::getX(), $pdf::getY(), $initialAndExpTimeString, 'LRB', 0, true, true, 'C', '', true);
    }
}
