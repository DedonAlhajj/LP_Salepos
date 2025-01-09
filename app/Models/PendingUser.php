<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    use HasFactory;
    protected $table = 'pending_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'store_name',
        'domain',
        'package_id',
        'operation_type',
        'status',
        'expires_at',
    ];


    protected $casts = [
        'expires_at' => 'datetime',
    ];


    public function package()
    {
        return $this->belongsTo(\App\Models\Package::class, 'package_id');
    }
}
