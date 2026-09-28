<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffRoster extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_date',
        'shift_type',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
