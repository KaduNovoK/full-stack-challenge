<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SpotifyService;
use App\Services\TrackService;
use App\Assemblers\TrackAssembler;

class SpotifyFetchTrack extends Command
{
    protected $signature = 'spotify:fetch-track {isrc}';
    protected $description = 'Fetch a track from Spotify by ISRC';

    protected SpotifyService $spotifyService;

    public function __construct(SpotifyService $spotifyService, TrackService $trackService)
    {
        parent::__construct();
        $this->spotifyService = $spotifyService;
        $this->trackService = $trackService;
    }

    public function handle()
    {
        $isrc = $this->argument('isrc');
        $dto = $this->spotifyService->getTrackByISRC($isrc);

        if (!$dto) {
            $this->error("Track not found for ISRC: $isrc");
            return 1;
        }

        $track = $this->trackService->createOrUpdateFromSpotifyTrackDTO($dto);

        // Apenas exibir por enquanto (sem persistir)
        $this->info("Track: {$track->title}");
        $this->info("Artists: {$track->artists}");
        $this->info("Duration: {$track->duration}");
        $this->info("Released: {$track->release_date}");
        $this->info("Available in BR: " . ($track->is_available_in_br ? 'Yes' : 'No'));

        return 0;
    }
}
