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
    public function reviewedVoucher()
    {
        return $this->hasOne(ReviewedVoucher::class);
    }
    public function approvedVoucher()
    {
        return $this->hasOne(ApprovedVoucher::class);
    }
    public function bankEndorsement()
    {
        return $this->hasOne(BankEndorsement::class);
    }
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
