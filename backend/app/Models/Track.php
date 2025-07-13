<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'isrc',
        'title',
        'artists',
        'thumb_url',
        'release_date',
        'duration',
        'preview_url',
        'spotify_url',
        'is_available_in_br',
    ];

    protected $casts = [
        'release_date' => 'date',
        'is_available_in_br' => 'boolean',
    ];
}
