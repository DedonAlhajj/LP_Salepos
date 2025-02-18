<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Brand extends Model
{
    use BelongsToTenant;
    use SoftDeletes;
    protected $fillable =[

        "title", "image", "page_title", "short_description", "slug", "is_active"
    ];

    public function product()
    {
    	return $this->hasMany('App\Models\Product');
    }
}
