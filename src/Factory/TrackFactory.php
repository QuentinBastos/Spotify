<?php

namespace App\Factory;

use App\Entity\Track;


class TrackFactory
{

    public function createMultipleFromSpotifyData(array $spotifyData): array
    {
        $tracks = [];

        foreach ($spotifyData as $item) {
            $track = new Track(
                $item['disc_number'],
                $item['duration_ms'],
                $item['explicit'],
                $item['external_ids']['isrc'],
                $item['external_urls']['spotify'],
                $item['href'],
                $item['id'],
                $item['is_local'],
                $item['name'],
                $item['popularity'],
                $item['preview_url'] ?? null,
                $item['track_number'],
                $item['type'],
                $item['uri'],
                $item['album']['images'][0]['url'] ?? null
            );
            $tracks[] = $track;
        }

        return $tracks;
    }

    public function createFromSpotifyData(array $spotifyData): Track {
        return new Track(
            $spotifyData['disc_number'],
            $spotifyData['duration_ms'],
            $spotifyData['explicit'],
            $spotifyData['external_ids']['isrc'],
            $spotifyData['external_urls']['spotify'],
            $spotifyData['href'],
            $spotifyData['id'],
            $spotifyData['is_local'],
            $spotifyData['name'],
            $spotifyData['popularity'],
            $spotifyData['preview_url'] ?? null,
            $spotifyData['track_number'],
            $spotifyData['type'],
            $spotifyData['uri'],
            $spotifyData['album']['images'][0]['url'] ?? null
        );
    }
}