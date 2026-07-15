<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
    protected $fillable = [
        'user_id',
        'phone_number',
        'time_frame_for_immigration',
        'address',
        'country_name',
        'country_id',
        'city',
        'state_name',
        'state_id',
        'county_id',
        'county_name',
        'zipcode',
        'have_broker',
        'have_attorney',
        'subscribe_for_newsletter',
        'broker_license',
        'attorney_license',
        'image',
        'about',
        'licensed_states',
    ];

    protected $casts = [
        'licensed_states' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
