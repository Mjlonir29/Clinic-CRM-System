<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'file_path',
        'file_type',
        'file_size',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
