<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListingType extends Model
{
    use SoftDeletes;

    protected $table = 'listing_types';

    protected $fillable = [
        'listing_type',
        'deleted_at'
    ];
}
