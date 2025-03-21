<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ["user_id", "from_date", "to_date", "note", "is_approved",'created_at'];


    public function user() {
    	return $this->belongsTo('App\Models\User');
    }

}
