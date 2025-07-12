<?php

namespace App\DTO\Spotify;

class SpotifyTrackDTO
{
    public string $id;
    public string $name;
    public string $uri;
    public ?string $previewUrl;

    public int $durationMs;
    public int $trackNumber;
    public int $discNumber;
    public int $popularity;

    public bool $explicit;
    public bool $isPlayable;

    public SpotifyAlbumDTO $album;
    public SpotifyExternalIdsDTO $externalIds;
    public SpotifyExternalUrlsDTO $externalUrls;

    /** @var SpotifyArtistDTO[] */
    public array $artists;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->durationMs = $data['duration_ms'];
        $this->explicit = $data['explicit'];
        $this->previewUrl = $data['preview_url'] ?? null;
        $this->uri = $data['uri'];
        $this->trackNumber = $data['track_number'];
        $this->discNumber = $data['disc_number'];
        $this->isPlayable = $data['is_playable'];
        $this->popularity = $data['popularity'];

        $this->album = new SpotifyAlbumDTO($data['album']);
        $this->artists = array_map(fn($artist) => new SpotifyArtistDTO($artist), $data['artists']);

        $this->externalIds = new SpotifyExternalIdsDTO($data['external_ids']);
        $this->externalUrls = new SpotifyExternalUrlsDTO($data['external_urls']);
    }
}
