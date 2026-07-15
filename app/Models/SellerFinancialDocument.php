<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerFinancialDocument extends Model
{
    use SoftDeletes;
    protected $fillable = ['business_information_id', 'document_path'];

    public function business_information()
    {
        return $this->belongsTo(BusinessInformation::class, 'business_information_id');
    }
}
