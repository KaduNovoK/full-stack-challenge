<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Jobs\FetchAndPersistTrackJob;
use App\Services\SpotifyService;
use App\DTO\Spotify\SpotifyTrackDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;

class FetchAndPersistTrackJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_persists_track_when_dto_is_returned()
    {
        $dto = new SpotifyTrackDTO([
            'id' => 'track123',
            'name' => 'Track Test',
            'duration_ms' => 123000,
            'explicit' => false,
            'preview_url' => null,
            'uri' => 'spotify:track:track123',
            'track_number' => 1,
            'disc_number' => 1,
            'is_playable' => true,
            'popularity' => 50,
            'album' => [
                'album_type' => 'album',
                'id' => 'album123',
                'name' => 'Album Test',
                'release_date' => '2020-01-01',
                'release_date_precision' => 'day',
                'total_tracks' => 10,
                'uri' => 'spotify:album:album123',
                'is_playable' => true,
                'available_markets' => ['BR'],
                'artists' => [],
                'images' => [
                    ['height' => 64, 'width' => 64, 'url' => 'https://example.com/thumb.jpg'],
                ],
                'external_urls' => ['spotify' => 'https://spotify.com/album/album123'],
            ],
            'artists' => [],
            'external_ids' => ['isrc' => 'ISRC123'],
            'external_urls' => ['spotify' => 'https://spotify.com/track/track123'],
        ]);

        $spotifyServiceMock = Mockery::mock(SpotifyService::class);
        $spotifyServiceMock->shouldReceive('getTrackByISRC')
            ->once()
            ->with('ISRC123')
            ->andReturn($dto);

        $this->app->instance(SpotifyService::class, $spotifyServiceMock);

        $job = new FetchAndPersistTrackJob('ISRC123');
        $job->handle($spotifyServiceMock);

        $this->assertDatabaseHas('tracks', [
            'title' => 'Track Test',
            'thumb_url' => 'https://example.com/thumb.jpg',
        ]);
    }

    public function test_it_logs_warning_if_track_not_found()
    {
        Log::shouldReceive('warning')
            ->once()
            ->with('Track not found for ISRC: ISRC404');

        $spotifyServiceMock = Mockery::mock(SpotifyService::class);
        $spotifyServiceMock->shouldReceive('getTrackByISRC')
            ->with('ISRC404')
            ->andReturn(null);

        $job = new FetchAndPersistTrackJob('ISRC404');
        $job->handle($spotifyServiceMock);
    }
}
