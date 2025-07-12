<?php

namespace App\DTO\Spotify;

class SpotifyArtistDTO
{
    public string $id;
    public string $name;
    public string $type;
    public string $uri;
    public string $href;

    public SpotifyExternalUrlsDTO $externalUrls;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->type = $data['type'];
        $this->uri = $data['uri'];
        $this->href = $data['href'];
        $this->externalUrls = new SpotifyExternalUrlsDTO($data['external_urls']);
    }
}
