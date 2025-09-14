<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attandance extends Model
{
    protected $table = 'attendance';
    
  protected $fillable = [
        'employeecode',
        'check_in_latitude',
        'check_in_longitude',
        'check_in',
        'check_out_latitude',
        'check_out_longitude',
        'check_out',
    ];

    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employeecode', 'employeecode');
    }
}