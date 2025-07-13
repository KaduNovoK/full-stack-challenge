<?php

namespace App\Services;

use App\Models\Track;
use App\DTO\Spotify\SpotifyTrackDTO;
use App\Assemblers\TrackAssembler;

class TrackService
{
    public function createOrUpdateFromSpotifyTrackDTO(SpotifyTrackDTO $dto): Track
    {
        $track = TrackAssembler::fromSpotifyDTO($dto);

        return Track::updateOrCreate(
            ['isrc' => $dto->externalIds->isrc],
            $track->getAttributes()
        );
    }
}
