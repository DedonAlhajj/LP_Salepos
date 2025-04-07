<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class GiftCard extends Model
{
    use BelongsToTenant;
    use SoftDeletes;

     protected $fillable =[
        "card_no", "amount", "expense", "customer_id", "user_id", "expired_date", "created_by"
    ];
}
