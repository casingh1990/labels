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

        $pdf::setCellMargins(-4,0,0,0);
        $pdf::setCellPaddings(0,0,0,0);

        $pdf::MultiCell(
            0,
            5,
            $this->inputs['sheet']
            . ' - PRINTED BY: '
            . $this->inputs['name']
            . ' - Expires '
            . Carbon::parse($this->inputs['exp_date'] . $this->inputs['exp_time'])->format('l, F j, Y h:i A'),
            0,
            'C',
            0,
            1,
            null,
            5
        );

         // set cell padding
        $pdf::setCellPaddings(1, 1, 1, 1);

        // set cell margins
        $pdf::setCellMargins(1, 1, 1, 1);

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
        $prepAndExpDatesString = '<table>'
            . '<tr><td>Prep Date'
            . '<u>' . Carbon::parse($this->inputs['prep_date'])->format('m/d/y') . '</u></td>'
            . '<td>Exp Date '
            . '<u>' . Carbon::parse($this->inputs['exp_date'])->format('m/d/y') . '</u></td></tr>'
            . '<tr><td>' . (strlen($this->inputs['name']) > 5 ? '' : 'Initial ')
            . '<u>' . $this->inputs['name'] . '</u></td>'
            . '<td>Exp Time '
            . '<u>' . $this->inputs['exp_time'] . '</u></td></tr></table>';

        //echo $prepAndExpDatesString;exit();
        $pdf::writeHTMLCell($this->width, 6.845, $pdf::getX(), $pdf::getY(), $prepAndExpDatesString, 'LRB', 0, true, true, 'C', '', true);
    }
}
