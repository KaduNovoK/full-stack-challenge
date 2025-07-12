<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SpotifyService;

class SpotifyFetchTrack extends Command
{
    protected $signature = 'spotify:fetch-track {isrc}';
    protected $description = 'Busca uma faixa no Spotify usando o ISRC';

    protected SpotifyService $spotifyService;

    public function __construct(SpotifyService $spotifyService)
    {
        parent::__construct();
        $this->spotifyService = $spotifyService;
    }

    public function handle(): int
    {
        $isrc = $this->argument('isrc');

        try {
            $track = $this->spotifyService->getTrackByISRC($isrc);

            if (!$track) {
                $this->warn("Nenhuma faixa encontrada para o ISRC: {$isrc}");
                return Command::FAILURE;
            }

            $this->info("Track ID: " . $track['id']);
            $this->info("Track Name: " . $track['name']);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Erro ao buscar faixa: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
