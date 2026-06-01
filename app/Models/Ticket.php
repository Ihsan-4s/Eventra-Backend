<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'ticket_code',
        'qr_code',
    ];

    // Relations
    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
