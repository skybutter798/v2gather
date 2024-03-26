<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class BvLog extends Model
{
    use Searchable;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeLeft($query)
    {
        return $query->where('position', Status::BV_LEFT);
    }
    public function scopeRight($query)
    {
        return $query->where('position', Status::BV_RIGHT);
    }
    public function scopeIncreaseTran($query)
    {
        return $query->where('trx_type', '+');
    }
    public function scopeDecreaseTran($query)
    {
        return $query->where('trx_type', '-');
    }
}