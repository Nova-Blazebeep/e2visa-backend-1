<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessType extends Model
{
    use SoftDeletes;
    protected $table = 'business_types';
    protected $fillable = [
        'business_type',
        'deleted_at'
    ];
}
