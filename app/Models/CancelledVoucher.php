<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelledVoucher extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function path()
    {
        return route('cancelled-vouchers.show', $this);
    }
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancel_user_id');
    }
}
