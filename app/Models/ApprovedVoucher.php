<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovedVoucher extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function path()
    {
        return route('approved-vouchers.show', $this);
    }
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
