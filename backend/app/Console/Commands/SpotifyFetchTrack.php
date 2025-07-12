<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SpotifyService;

class SpotifyFetchTrack extends Command
{
    protected $signature = 'spotify:fetch-track {isrc}';
    protected $description = 'Fetch a track from Spotify by ISRC';

    protected SpotifyService $spotifyService;

    public function __construct(SpotifyService $spotifyService)
    {
        parent::__construct();
        $this->spotifyService = $spotifyService;
    }

    public function handle()
    {
        $isrc = $this->argument('isrc');
        $track = $this->spotifyService->getTrackByISRC($isrc);

        if (!$track) {
            $this->error("Track not found for ISRC: $isrc");
            return 1;
        }

        $this->info("Track ID: {$track->id}");
        $this->info("Track Name: {$track->name}");

        return 0;
    }
}
