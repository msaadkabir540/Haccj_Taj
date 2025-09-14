<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledNotification extends Model
{
    protected $fillable = [
        'employeecode','title','body',
        'day_of_week','send_time','timezone',
        'is_recurring','last_sent_at','status',
    ];

    protected $casts = [
        'is_recurring' => 'bool',
        'last_sent_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employeecode', 'employeecode');
    }
}
