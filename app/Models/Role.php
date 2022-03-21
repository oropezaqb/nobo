<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
    public function allowTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::whereName($permission)->firstOrFail();
        }
        $this->permissions()->sync($permission, false);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
