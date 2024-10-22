<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SpotifyService
{
    private string $clientId;
    private string $clientSecret;
    private HttpClientInterface $httpClient;
    private RequestStack $requestStack;

    public function __construct(HttpClientInterface $httpClient, RequestStack $requestStack, string $clientId, string $clientSecret)
    {
        $this->httpClient = $httpClient;
        $this->requestStack = $requestStack;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function auth(): string
    {
        $newTokenNeeded = false;
        $session = $this->requestStack->getSession();
        if (!$session->has('token') || $session->get('expire') <= time()) {
            $newTokenNeeded = true;
        }

        if ($newTokenNeeded) {
            $response = $this->httpClient->request('POST', 'https://accounts.spotify.com/api/token', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => [
                    'grant_type' => 'client_credentials',
                ],
            ]);

            $data = $response->toArray();


            $session->set('token', $data['access_token']);
            $session->set('expire', time() + 3600);
            return $data['access_token'];
        } else {
            return $session->get('token');
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getArtist(string $searchQuery, string $token): ResponseInterface
    {
        return $this->httpClient->request('GET', 'https://api.spotify.com/v1/search', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'query' => [
                'q' => $searchQuery,
                'type' => 'artist',
                'locale' => 'fr-FR',
            ],
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getArtistById(string $id, string $token): ResponseInterface
    {
        return $this->httpClient->request('GET', 'https://api.spotify.com/v1/artists/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getTrack(string $searchQuery, string $token): ResponseInterface
    {
        return $this->httpClient->request('GET', 'https://api.spotify.com/v1/search', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'query' => [
                'q' => $searchQuery,
                'type' => 'track',
                'locale' => 'fr-FR',
            ],
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getTrackSearchRandom(string $token): ResponseInterface
    {
        return $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/search?query=kazzey&type=track&locale=fr-FR', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getTrackByRecommendations(string $token, string $id = '2K5cF4TM3vH1eaX0eqXfzZ'): ResponseInterface
    {
        return $this->httpClient->request('GET', 'https://api.spotify.com/v1/recommendations', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'query' => [
                'seed_tracks' => $id,
            ],
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getTrackById(string $id, string $token): ResponseInterface
    {
        return $this->httpClient->request('GET', 'https://api.spotify.com/v1/tracks/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
    }
}
