<?php

namespace App\Services;

use App\Models\SheetConfig;
use Carbon\Carbon;
use Elibyy\TCPDF\Facades\TCPDF;

class SheetService
{
    public const COLOR_RED = 0;
    public const COLOR_GREEN = 1;
    public const COLOR_BLUE = 2;

    protected float $width;

    protected array $inputs;

    protected TCPDF $pdf;

    public function generate(array $inputs)
    {
        $this->inputs = $inputs;

        $this->pdf = new TCPDF();
        $this->pdf::SetTitle($inputs['sheet']);
        $this->pdf::AddPage('', 'LETTER');

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

        $this->pdf::setCellMargins(-4,0,0,0);
        $this->pdf::setCellPaddings(0,0,0,0);
        $this->pdf::setDrawColorArray([192,212,219]);

        $this->pdf::MultiCell(
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
        $this->pdf::setCellPaddings(1, 1, 1, 1);

        // set cell margins
        $this->pdf::setCellMargins(1, 1, 1, 1);

        for ($i = 0; $i < $count; $i++) {
            $this->writeRow($i, $sheetConfigByCoordinates);
            $this->pdf::Ln();
        }

        $this->pdf::Ln(4);

        $this->pdf::Output($inputs['sheet'] . '.pdf');
    }

    protected function writeRow($rowNum, $sheetConfigByCoordinates)
    {
        $this->pdf::setCellMargins(0, 0, 1, 0);
        $this->writeLabel($sheetConfigByCoordinates[$rowNum][1]);
        $this->pdf::setCellMargins(6.5, 0, 1, 0);
        $this->writeLabel($sheetConfigByCoordinates[$rowNum][2]);
        $this->writeLabel($sheetConfigByCoordinates[$rowNum][3]);
        $this->writeLabel($sheetConfigByCoordinates[$rowNum][4]);
        $this->pdf::setCellMargins(0, 0, 1, 0);

        $this->pdf::Ln();

        $this->writePrepAndExpDates($sheetConfigByCoordinates[$rowNum][1]);
        $this->pdf::setCellMargins(6.5, 0, 1, 0);
        $this->writePrepAndExpDates($sheetConfigByCoordinates[$rowNum][2]);
        $this->writePrepAndExpDates($sheetConfigByCoordinates[$rowNum][3]);
        $this->writePrepAndExpDates($sheetConfigByCoordinates[$rowNum][4]);
        $this->pdf::setCellMargins(0, 0, 1, 0);
    }

    protected function writeLabel(SheetConfig $labelConfig)
    {
        $this->pdf::setFontSize(10);
        $nameAndDosage = $labelConfig->medication->name
            . ' ' . $labelConfig->dosage
            . ' <small>' . $labelConfig->unit->name . '</small>';

        $this->setCellFillFromConfigHeader($labelConfig);
        $this->pdf::writeHTMLCell($this->width, 5, $this->pdf::getX(), $this->pdf::getY(), $nameAndDosage, 'LTR', 0, true, true, 'C', '', true);
    }

    protected function writePrepAndExpDates(SheetConfig $labelConfig)
    {
        $this->pdf::setFontSize(6.5);
        $prepAndExpDatesString = '<table>'
            . '<tr><td>Prep Date'
            . '<u>' . Carbon::parse($this->inputs['prep_date'])->format('m/d/y') . '</u></td>'
            . '<td>Exp Date '
            . '<u>' . Carbon::parse($this->inputs['exp_date'])->format('m/d/y') . '</u></td></tr>'
            . '<tr><td>' . (strlen($this->inputs['name']) > 5 ? '' : 'Initial ')
            . '<u>' . $this->inputs['name'] . '</u></td>'
            . '<td>Exp Time '
            . '<u>' . $this->inputs['exp_time'] . '</u></td></tr></table>';

        $this->setCellFillFromConfig($labelConfig);
        $this->pdf::writeHTMLCell($this->width, 6.845, $this->pdf::getX(), $this->pdf::getY(), $prepAndExpDatesString, 'LRB', 0, true, true, 'C', '', true);
    }
    protected function setCellFillFromConfigHeader(SheetConfig $labelConfig)
    {
        if (intval($labelConfig->black_header) === 0) {
            $this->setCellFillFromConfig($labelConfig);
        } else {
            $this->pdf::setTextColorArray([255,255,255]);
            $this->pdf::setFillColor(0,0,0);
        }
    }

    protected function setCellFillFromConfig(SheetConfig $labelConfig)
    {
        $this->pdf::setTextColorArray([0,0,0]);
        $colorValues = explode(',', $labelConfig->color);
        $this->pdf::setFillColor(
            $colorValues[self::COLOR_RED],
            $colorValues[self::COLOR_GREEN],
            $colorValues[self::COLOR_BLUE]
        );
    }
}
