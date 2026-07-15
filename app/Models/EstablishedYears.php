<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstablishedYears extends Model
{
    use SoftDeletes;
    protected $table = 'established_years';

    protected $fillable = [
        'year',
        'deleted_at',
    ];
}
