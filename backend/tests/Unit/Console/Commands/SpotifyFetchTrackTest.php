<?php

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;
use App\Services\SpotifyService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\App;
use Symfony\Component\Console\Command\Command;
use Illuminate\Testing\PendingCommand;

class SpotifyFetchTrackTest extends TestCase
{
    public function test_command_outputs_track_info_when_found()
    {
        $mockService = $this->createMock(SpotifyService::class);
        $mockService->method('getTrackByISRC')
            ->willReturn(['id' => '123abc', 'name' => 'Track Name Test']);

        App::instance(SpotifyService::class, $mockService);

        $this->artisan('spotify:fetch-track FAKEISRC')
            ->expectsOutput('Track ID: 123abc')
            ->expectsOutput('Track Name: Track Name Test')
            ->assertExitCode(Command::SUCCESS);
    }

    public function test_command_outputs_warning_when_track_not_found()
    {
        $mockService = $this->createMock(SpotifyService::class);
        $mockService->method('getTrackByISRC')
            ->willReturn(null);

        App::instance(SpotifyService::class, $mockService);

        $this->artisan('spotify:fetch-track FAKEISRC')
            ->expectsOutput('Nenhuma faixa encontrada para o ISRC: FAKEISRC')
            ->assertExitCode(Command::FAILURE);
    }

    public function test_command_outputs_error_on_exception()
    {
        $mockService = $this->createMock(SpotifyService::class);
        $mockService->method('getTrackByISRC')
            ->willThrowException(new \Exception('Erro simulado'));

        App::instance(SpotifyService::class, $mockService);

        $this->artisan('spotify:fetch-track FAKEISRC')
            ->expectsOutput('Erro ao buscar faixa: Erro simulado')
            ->assertExitCode(Command::FAILURE);
    }
}
