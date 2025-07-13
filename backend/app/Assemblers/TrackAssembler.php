<?php

namespace App\Assemblers;

use App\DTO\Spotify\SpotifyTrackDTO;
use App\Models\Track;

class TrackAssembler
{
    public static function fromSpotifyDTO(SpotifyTrackDTO $dto): Track
    {
        // Extrai os nomes dos artistas como string separada por vírgula
        $artistNames = implode(', ', array_map(fn($artist) => $artist->name, $dto->artists));

        // Pega a menor imagem do álbum (última do array)
        $albumThumb = null;
        if (!empty($dto->album->images)) {
            $albumThumb = end($dto->album->images)->url;
        }

        // Verifica se a faixa está disponível no mercado BR
        $isAvailableInBrazil = in_array('BR', $dto->album->availableMarkets ?? []);

        // Formata a duração em mm:ss
        $durationFormatted = self::formatDuration($dto->durationMs);

        return new Track([
            'title' => $dto->name,
            'artists' => $artistNames,
            'thumb_url' => $albumThumb,
            'release_date' => $dto->album->releaseDate,
            'duration' => $durationFormatted,
            'preview_url' => $dto->previewUrl,
            'spotify_url' => $dto->externalUrls->spotify,
            'is_available_in_br' => $isAvailableInBrazil,
        ]);
    }

    private static function formatDuration(int $durationMs): string
    {
        $seconds = intdiv($durationMs, 1000);
        $minutes = intdiv($seconds, 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }
}
