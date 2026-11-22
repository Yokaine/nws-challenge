<?php

namespace App\Game\Service;

use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MusicService
{
    private const API_URL = 'https://api.freetouse.com/v3';

    public function __construct(
        private HttpClientInterface $httpClient
    ) {
    }

    public function getChillTracks(): array
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                self::API_URL . '/music/tracks/search',
                [
                    'query' => [
                        'query' => 'lofi chill ambient instrumental',
                        'limit' => 20,
                        'order' => 'plays',
                        'sort' => 'desc',
                    ],
                    'timeout' => 10,
                ]
            );

            $data = $response->toArray();

            if (!($data['ok'] ?? false)) {
                return [];
            }

            $tracks = [];

            foreach ($data['data'] ?? [] as $track) {
                $audioUrl = $track['files']['mp3'] ?? null;

                if (
                    !is_string($audioUrl) ||
                    $audioUrl === '' ||
                    ($track['is_premium'] ?? true)
                ) {
                    continue;
                }

                $artists = [];

                foreach ($track['artists'] ?? [] as $artistEntry) {
                    if (isset($artistEntry[1]['name'])) {
                        $artists[] = $artistEntry[1]['name'];
                    }
                }

                $tracks[] = [
                    'id' => $track['id'] ?? '',
                    'title' => $track['title'] ?? 'Titre inconnu',
                    'artist' => implode(', ', $artists) ?: 'Artiste indépendant',
                    'genre' => $track['genre'] ?? 'Instrumental',
                    'duration' => (int) ($track['duration'] ?? 0),
                    'cover' => $track['thumbnails']['md'] ?? null,
                    'audio' => $audioUrl,
                ];
            }

            return $tracks;
        } catch (ExceptionInterface $exception) {
            return [];
        }
    }
}