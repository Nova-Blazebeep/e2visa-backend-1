<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessImage extends Model
{
    use SoftDeletes;
    protected $fillable = ['business_information_id', 'image_path'];

    public function business_information()
    {
        return $this->belongsTo(BusinessInformation::class, 'business_information_id');
    }
}
