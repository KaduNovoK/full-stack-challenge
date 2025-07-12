<?php

namespace App\DTO\Spotify;

class SpotifyExternalUrlsDTO
{
    public string $spotify;

    public function __construct(array $data)
    {
        $this->spotify = $data['spotify'];
    }
}
