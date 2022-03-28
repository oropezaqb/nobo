<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function path()
    {
        return route('vouchers.show', $this);
    }
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
    public function approvedVoucher()
    {
        return $this->hasOne(ApprovedVoucher::class);
    }
}
