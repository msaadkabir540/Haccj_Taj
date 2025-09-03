<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attandance extends Model
{
    protected $table = 'attandance';
    protected $fillable = ['location', 'time', 'employeecode']; // ✅ fix here

    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employeecode', 'employeecode');
    }
}