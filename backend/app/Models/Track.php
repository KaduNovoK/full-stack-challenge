<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'artists',
        'duration',
        'thumb_url',
        'preview_url',
        'spotify_url',
        'release_date',
        'is_available_in_br',
    ];

    protected $casts = [
        'release_date' => 'date',
        'is_available_in_br' => 'boolean',
    ];
}
