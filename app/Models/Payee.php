<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payee extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function path()
    {
        return route('payees.show', $this);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
