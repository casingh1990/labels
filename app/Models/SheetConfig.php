<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SheetConfig extends Model
{
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    public function sheet()
    {
        return $this->belongsTo(Sheet::class);
    }
}
