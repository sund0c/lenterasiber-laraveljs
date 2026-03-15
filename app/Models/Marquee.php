<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marquee extends Model
{
    protected $table = 'marquees';

    protected $fillable = [
        'title',
        'published_date',
        'status',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'published_date' => 'date',
        'published_at'   => 'datetime',
    ];
}
