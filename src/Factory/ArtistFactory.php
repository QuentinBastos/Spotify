<?php

namespace App\Factory;

use App\Entity\Artist;

class ArtistFactory
{
    public function createMultipleFromSpotifyData(array $spotifyData): array
    {
        $artists = [];
        foreach ($spotifyData as $item) {
            $artist = new Artist(
                $item['id'],
                $item['external_urls']['spotify'],
                $item['followers']['total'],
                $item['genres'],
                $item['href'],
                $item['images'],
                $item['name'],
                $item['popularity'],
                $item['type'],
                $item['uri'],
            );
            $artists[] = $artist;
        }
        return $artists;
    }

    public function createFromSpotifyData(array $spotifyData): Artist
    {
        return new Artist(
            $spotifyData['id'],
            $spotifyData['external_urls']['spotify'],
            $spotifyData['followers']['total'],
            $spotifyData['genres'],
            $spotifyData['href'],
            $spotifyData['images'],
            $spotifyData['name'],
            $spotifyData['popularity'],
            $spotifyData['type'],
            $spotifyData['uri'],
        );
    }
}