<?php

namespace App\Jobs;

use App\Services\SpotifyService;
use App\Services\TrackService;
use App\Assemblers\TrackAssembler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Track;

class FetchAndPersistTrackJob
{
    use Dispatchable, Queueable;

    public string $isrc;

    public function __construct(string $isrc)
    {
        $this->isrc = $isrc;
    }

    public function handle(SpotifyService $spotifyService, TrackService $trackService): void
    {
        $dto = $spotifyService->getTrackByISRC($this->isrc);

        if (!$dto) {
            // loga, notifica, etc.
            logger()->warning("Track not found for ISRC: {$this->isrc}");
            return;
        }

        $trackService->createOrUpdateFromSpotifyTrackDTO($dto);
    }
}
