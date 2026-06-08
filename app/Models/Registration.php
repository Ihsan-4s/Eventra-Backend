<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

class Registration extends Model
{
    use HasFactory;
    protected $fillable = [
        'registration_code',
        'event_id',
        'full_name',
        'email',
        'phone_number',
        'registration_date',
        'status'
    ];

    protected function casts():array
    {
        return [
            'registration_date' => 'datetime',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }
}
