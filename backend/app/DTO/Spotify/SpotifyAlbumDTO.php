<?php

namespace App\DTO\Spotify;

class SpotifyAlbumDTO
{
    public string $albumType;
    public string $id;
    public string $name;
    public string $releaseDate;
    public string $releaseDatePrecision;
    public string $uri;

    public int $totalTracks;

    public bool $isPlayable;

    public SpotifyExternalUrlsDTO $externalUrls;

    /** @var SpotifyArtistDTO[] */
    public array $artists;

    /** @var SpotifyImageDTO[] */
    public array $images;

    /** @var string[] */
    public array $availableMarkets;

    public function __construct(array $data)
    {
        $this->albumType = $data['album_type'];
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->releaseDate = $data['release_date'];
        $this->releaseDatePrecision = $data['release_date_precision'];
        $this->totalTracks = $data['total_tracks'];
        $this->uri = $data['uri'];
        $this->isPlayable = $data['is_playable'] ?? false;
        $this->availableMarkets = $data['available_markets'] ?? [];

        $this->artists = array_map(fn($artist) => new SpotifyArtistDTO($artist), $data['artists']);
        $this->images = array_map(fn($image) => new SpotifyImageDTO($image), $data['images']);
        $this->externalUrls = new SpotifyExternalUrlsDTO($data['external_urls']);
    }
}
