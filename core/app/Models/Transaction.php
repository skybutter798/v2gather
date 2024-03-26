<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use Searchable;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    protected $fillable = [
        // Other existing fillable fields...
        'user_id',
        'amount',
        'post_balance',
        'charge',
        'trx_type',
        'details',
        'trx',
        'remark',
        // Add any other fields you want to be mass-assignable
    ];

}
