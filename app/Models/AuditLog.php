<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'module',
        'ip_address',
        'user_agent',
        'details',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function record($action, $module, $details = null)
    {
        $user = auth()->user();
        return self::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? ($user->name ?? $user->full_name) : 'Guest / Patient',
            'user_role' => $user ? ($user->role ?? 'Staff') : 'Patient',
            'action' => $action,
            'module' => $module,
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->header('User-Agent', ''), 0, 250),
            'details' => is_array($details) ? json_encode($details) : $details,
            'created_at' => now(),
        ]);
    }
}
