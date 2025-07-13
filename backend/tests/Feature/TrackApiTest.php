<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Track;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrackApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_tracks_ordered_by_title()
    {
        // Criar alguns tracks no banco
        Track::factory()->create(['title' => 'B Song']);
        Track::factory()->create(['title' => 'A Song']);
        Track::factory()->create(['title' => 'C Song']);

        $response = $this->getJson('/api/track');

        $response->assertStatus(200);

        // Verificar ordem alfabética pelo título
        $titles = array_column($response->json(), 'title');
        $this->assertEquals(['A Song', 'B Song', 'C Song'], $titles);
    }

    public function test_show_returns_track_details()
    {
        $track = Track::factory()->create([
            'title' => 'Test Track',
            'artists' => 'Artist 1, Artist 2',
            'release_date' => '2023-01-01',
            'duration' => '03:45',
            'preview_url' => 'https://example.com/preview.mp3',
            'spotify_url' => 'https://spotify.com/track/test',
            'is_available_in_br' => true,
            'thumb_url' => 'https://example.com/thumb.jpg',
            'isrc' => 'USRC17607839',
        ]);

        $response = $this->getJson("/api/track/{$track->id}");

        $response->assertJsonFragment([
            'id' => $track->id,
            'title' => 'Test Track',
            'artists' => 'Artist 1, Artist 2',
            'duration' => '03:45',
            'preview_url' => 'https://example.com/preview.mp3',
            'spotify_url' => 'https://spotify.com/track/test',
            'is_available_in_br' => true,
            'thumb_url' => 'https://example.com/thumb.jpg',
        ]);

        $this->assertStringStartsWith('2023-01-01', $response->json('release_date'));
    }

    public function test_show_returns_404_for_invalid_id()
    {
        $response = $this->getJson('/api/track/999999');

        $response->assertStatus(404);
    }
}
