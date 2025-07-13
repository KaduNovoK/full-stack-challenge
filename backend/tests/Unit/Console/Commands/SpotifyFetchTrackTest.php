<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\SpotifyService;
use App\DTO\Spotify\SpotifyTrackDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class SpotifyFetchTrackTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_saves_track_and_outputs_info()
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
                    'available_markets' => ['BR'],
                    'artists' => [],
                    'images' => [
                        [
                            'height' => 64,
                            'width' => 64,
                            'url' => 'https://example.com/thumb.jpg',
                        ]
                    ],
                    'external_urls' => ['spotify' => 'https://spotify.com/album/album123'],
                ],
                'artists' => [],
                'external_ids' => ['isrc' => 'ISRC123'],
                'external_urls' => ['spotify' => 'https://spotify.com/track/track123'],
            ]));

        $this->app->instance(SpotifyService::class, $spotifyServiceMock);

        $this->artisan('spotify:fetch-track', ['isrc' => 'ISRC123'])
            ->expectsOutput('Track: Track Test')
            ->expectsOutput('Artists: ') // vazio porque mock não tem artistas
            ->expectsOutput('Duration: 02:03') // 123000 ms formatados em mm:ss
            ->expectsOutputToContain('Released: 2020-01-01')
            ->expectsOutput('Available in BR: Yes')
            ->assertExitCode(0);

        $this->assertDatabaseHas('tracks', [
            'title' => 'Track Test',
            'release_date' => '2020-01-01',
            'is_available_in_br' => true,
            'thumb_url' => 'https://example.com/thumb.jpg',
        ]);
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
