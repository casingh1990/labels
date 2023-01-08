<?php

namespace App\Http\Controllers;

use App\Imports\SheetsImport;
use App\Models\SheetConfig;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Elibyy\TCPDF\Facades\TCPDF;

class LabelController extends Controller
{
    public function generate(Request $request)
    {
        $pdf = new TCPDF();
        $pdf::SetTitle($request->get('sheet'));
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



        $width = 44.5;
        $count = 10;

        for ($i = 0; $i < $count; $i++) {
            // Multicell test
            $pdf::setCellMargins(0, 1, 1, 1);
            $pdf::MultiCell($width, 5, '[LEFT] '.$txt, 1, 'L', 1, 0, '', '', true);
            $pdf::setCellMargins(6.5, 1, 1, 1);
            $pdf::MultiCell($width, 5, '[LEFT] '.$txt, 1, 'L', 1, 0, '', '', true);
            $pdf::MultiCell($width, 5, '[LEFT] '.$txt, 1, 'L', 1, 0, '', '', true);
            $pdf::MultiCell($width, 5, '[LEFT] '.$txt, 1, 'L', 1, 0, '', '', true);

            $pdf::Ln();
        }

        $pdf::Ln(4);

        $pdf::Output('hello_world.pdf');/*/

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml('hello world');

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream();//*/
    }

    protected function writeLabel($width)
    {
        $pdf::MultiCell($width, 5, '[LEFT] '.$txt, 1, 'L', 1, 0, '', '', true);
    }

    public function preview(Request $request)
    {

    }

    public function importLabelConfig(Request $request)
    {
        $sheets = [];
        $medications = [];
        $units = [];

        SheetConfig::unguard();

        SheetConfig::truncate();

        Excel::import(new SheetsImport($sheets, $medications, $units), $request->file);

        SheetConfig::reguard();
    }
}
