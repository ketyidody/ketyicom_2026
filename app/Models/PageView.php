<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $fillable = [
        'session_id',
        'url',
        'referrer',
        'country',
        'country_code',
        'user_agent',
    ];
}
