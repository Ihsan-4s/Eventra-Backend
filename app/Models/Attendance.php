<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'check_in_time',
        'attendance_status',
    ];

    protected function casts(): array
    {
        return [
            'check_in_time' => 'datetime',
        ];
    }

    // Relations
    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
