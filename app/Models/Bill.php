<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function path()
    {
        return route('bills.show', $this);
    }
    public function payee()
    {
        return $this->belongsTo(Payee::class, 'payee_id');
    }
}
