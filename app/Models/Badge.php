<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class Badge extends Model
{
    use SoftDeletes;

    protected $fillable = ['role_id', 'icon'];


    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
