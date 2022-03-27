<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function path()
    {
        return route('payments.show', $this);
    }
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
