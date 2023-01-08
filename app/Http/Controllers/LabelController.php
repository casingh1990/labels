<?php

namespace App\Http\Controllers;

use App\Imports\SheetsImport;
use App\Models\SheetConfig;
use App\Services\SheetService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Elibyy\TCPDF\Facades\TCPDF;

class LabelController extends Controller
{
    public function generate(Request $request, SheetService $sheetService)
    {
        return $sheetService->generate($request->all());
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
