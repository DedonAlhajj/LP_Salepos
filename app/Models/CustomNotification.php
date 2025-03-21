<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;

class CustomNotification extends DatabaseNotification
{
    protected $table = 'notifications'; // ربطه بجدول Laravel الأساسي

    protected $fillable = [
        'id',
        'sender_id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
        'created_at',
        'updated_at',
        'status', // الحقول الجديدة
        'batch_id',
        'error_message',
        'channels',
        'failed_channels',
        'sent_at'
    ];

    protected $casts = [
        'data' => 'array',
        'channels' => 'array',
        'failed_channels' => 'array',
    ];


    public function user()
    {
        return $this->belongsTo('App\Models\User','notifiable_id');
    }

    public function userFrom()
    {
        return $this->belongsTo('App\Models\User','sender_id');
    }
}
