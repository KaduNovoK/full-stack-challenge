<?php

namespace App\DTO\Spotify;

class SpotifyImageDTO
{
    public string $url;

    public int $height;
    public int $width;

    public function __construct(array $data)
    {
        $this->height = $data['height'];
        $this->width = $data['width'];
        $this->url = $data['url'];
    }
}
