<?php

namespace App\Imports;

use App\Models\Medication;
use App\Models\Sheet;
use App\Models\SheetConfig;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SheetsImport implements ToModel, WithHeadingRow
{
    use RemembersRowNumber;

    public function __construct(
        protected &$sheets,
        protected &$medications,
        protected &$units,
    ) {

    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (empty($row['sheet']) || empty($row['medication']) || empty($row['unit'])) {
            return null;
        }

        $sheet = $this->getSheet($row['sheet']);
        $medication = $this->getMedication($row['medication']);
        $unit = $this->getUnit($row['unit']);

        $rowNumberToUse = $this->getRowNumber() - 2;
        $columnNum = floor($rowNumberToUse / 20) + 1;
        $rowNum = ($rowNumberToUse % 20);

        return new SheetConfig([
            'sheet_id' => $sheet->id,
            'medication_id' => $medication->id,
            'column' => $columnNum,
            'row' => $rowNum,
            'dosage' => $row['dosage'],
            'color' => $row['color'],
            'black_header' => intval($row['black_header']),
            'unit_id' => $unit->id,
        ]);
    }

    protected function getSheet($name)
    {
        if (!isset($this->sheets[$name])) {
            $this->sheets[$name] = Sheet::firstOrCreate(
                ['name' => $name]
            );
        }

        return $this->sheets[$name];
    }

    protected function getMedication($name)
    {
        if (!isset($this->medications[$name])) {
            $this->medications[$name] = Medication::firstOrCreate(
                ['name' => $name]
            );
        }

        return $this->medications[$name];
    }

    protected function getUnit($name)
    {
        if (!isset($this->units[$name])) {
            $this->units[$name] = Unit::firstOrCreate(
                ['name' => $name]
            );
        }

        return $this->units[$name];
    }
}
