<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\SpotifyService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class SpotifyServiceTest extends TestCase
{
    protected SpotifyService $spotifyService;

    protected function setUp(): void
    {
        parent::setUp();

        // Configurar as credenciais para o teste
        Config::set('services.spotify.client_id', 'fake_client_id');
        Config::set('services.spotify.client_secret', 'fake_client_secret');

        $this->spotifyService = new SpotifyService();
    }

    public function test_get_access_token_returns_token_and_caches_it()
    {
        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response([
                'access_token' => 'fake_token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200)
        ]);

        Cache::flush();

        // Primeira chamada: deve buscar o token e armazenar em cache
        $token = $this->spotifyService->getAccessToken();
        $this->assertEquals('fake_token', $token);

        // Alterar o fake para um token diferente se a função for chamada novamente (não deveria ser)
        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response([
                'access_token' => 'another_token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200)
        ]);

        // Segunda chamada: deve retornar o token do cache (fake_token)
        $cachedToken = $this->spotifyService->getAccessToken();
        $this->assertEquals('fake_token', $cachedToken);
    }

    public function test_get_access_token_throws_exception_on_failure()
    {
        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response([], 500)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao obter token do Spotify');

        $this->spotifyService->getAccessToken();
    }

    public function test_get_track_by_isrc_returns_track_data()
    {
        // Mock do token
        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response([
                'access_token' => 'fake_token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://api.spotify.com/v1/search*' => Http::response([
                'tracks' => [
                    'items' => [
                        ['id' => 'track123', 'name' => 'Track Test']
                    ]
                ]
            ], 200)
        ]);

        Cache::flush();

        $track = $this->spotifyService->getTrackByISRC('FAKEISRC123');
        $this->assertIsArray($track);
        $this->assertEquals('track123', $track['id']);
        $this->assertEquals('Track Test', $track['name']);
    }

    public function test_get_track_by_isrc_returns_null_on_failure()
    {
        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response([
                'access_token' => 'fake_token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://api.spotify.com/v1/search*' => Http::response([], 500)
        ]);

        Cache::flush();

        $track = $this->spotifyService->getTrackByISRC('FAKEISRC123');
        $this->assertNull($track);
    }
}
