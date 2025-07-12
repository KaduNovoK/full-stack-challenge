<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\SpotifyService;
use App\Console\Commands\SpotifyFetchTrack;
use App\DTO\Spotify\SpotifyTrackDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class SpotifyFetchTrackTest extends TestCase
{
    public function test_handle_returns_track_info()
    {
        $spotifyServiceMock = Mockery::mock(SpotifyService::class);
        $spotifyServiceMock->shouldReceive('getTrackByISRC')
            ->with('ISRC123')
            ->andReturn(new SpotifyTrackDTO([
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
                    'available_markets' => ['US'],
                    'artists' => [],
                    'images' => [],
                    'external_urls' => ['spotify' => 'https://spotify.com/album/album123'],
                ],
                'artists' => [],
                'external_ids' => ['isrc' => 'ISRC123'],
                'external_urls' => ['spotify' => 'https://spotify.com/track/track123'],
            ]));

        $this->app->instance(SpotifyService::class, $spotifyServiceMock);

        $this->artisan('spotify:fetch-track', ['isrc' => 'ISRC123'])
             ->expectsOutput('Track ID: track123')
             ->expectsOutput('Track Name: Track Test')
             ->assertExitCode(0);
    }

    public function test_handle_returns_error_when_track_not_found()
    {
        $spotifyServiceMock = Mockery::mock(SpotifyService::class);
        $spotifyServiceMock->shouldReceive('getTrackByISRC')
            ->with('ISRC123')
            ->andReturn(null);

        $this->app->instance(SpotifyService::class, $spotifyServiceMock);

        $this->artisan('spotify:fetch-track', ['isrc' => 'ISRC123'])
             ->expectsOutput('Track not found for ISRC: ISRC123')
             ->assertExitCode(1);
    }
}
