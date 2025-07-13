<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Bus;
use App\Jobs\FetchAndPersistTrackJob;

class SpotifyBatchFetchTracksTest extends TestCase
{
    public function test_it_dispatches_jobs_for_each_isrc()
    {
        Bus::fake();

        $this->artisan('spotify:batch-fetch')
            ->expectsOutput('Processing ISRC: US7VG1846811')
            ->expectsOutput('Processing ISRC: QZNJX2078148')
            ->expectsOutput('All ISRCs processed.')
            ->assertExitCode(0);

        $expectedIsrcs = [
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

        foreach ($expectedIsrcs as $isrc) {
            Bus::assertDispatchedSync(FetchAndPersistTrackJob::class, function ($job) use ($isrc) {
                return $job->isrc === $isrc;
            });
        }
    }
}
