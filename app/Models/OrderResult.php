<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderResult extends Model
{
    protected $fillable = [
        'order_id',
        'file_name',
        'file_path',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}