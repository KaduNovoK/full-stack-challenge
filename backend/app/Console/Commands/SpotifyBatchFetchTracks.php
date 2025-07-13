<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchAndPersistTrackJob;

class SpotifyBatchFetchTracks extends Command
{
    protected $signature = 'spotify:batch-fetch';
    protected $description = 'Fetch and persist multiple tracks by ISRC';

    public function handle()
    {
        $isrcs = [
            'US7VG1846811',
            'US7QQ1846811',
            'BRC310600002',
            'BR1SP1200071',
            'BR1SP1200070',
            'BR1SP1500002',
            'BXKZM1900338',
            'BXKZM1900345',
            'QZNJX2081700',
            'QZNJX2078148',
        ];

        foreach ($isrcs as $isrc) {
            $this->info("Processing ISRC: $isrc");
            FetchAndPersistTrackJob::dispatchSync($isrc);
        }

        $this->info('All ISRCs processed.');
        return 0;
    }
}
