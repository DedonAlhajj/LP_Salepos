<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature_package extends Model
{
    use HasFactory;
    protected $fillable = [
        'feature_id',
        'package_id',
    ];
}
