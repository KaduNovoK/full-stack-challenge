<?php

namespace App\DTO\Spotify;

class SpotifyExternalIdsDTO
{
    public string $isrc;

    public function __construct(array $data)
    {
        $this->isrc = $data['isrc'];
    }
}
