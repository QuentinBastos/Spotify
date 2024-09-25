<?php

namespace App\Controller;

use App\Factory\TrackFactory;
use App\Form\SearchType;
use App\Service\AuthSpotifyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TrackController extends AbstractController
{
    private string $token;
    public TrackFactory $trackFactory;

    public function __construct(
        private readonly AuthSpotifyService $authSpotifyService,
        private readonly HttpClientInterface $httpClient,
        TrackFactory $trackFactory
    ) {
        $this->token = $this->authSpotifyService->auth();
        $this->trackFactory = $trackFactory;
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/tracks', name: 'app_tracks')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchQuery = $form->get('search')->getData();

            $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/search', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                ],
                'query' => [
                    'q' => $searchQuery,
                    'type' => 'track',
                    'locale' => 'fr-FR',
                ],
            ]);

        } else {
            $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/search?query=kazzey&type=track&locale=fr-FR', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                ],
            ]);
        }
        $tracks = $this->trackFactory->createMultipleFromSpotifyData($response->toArray()['tracks']['items']);

        return $this->render('track/index.html.twig', [
            'tracks' => $tracks,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/tracks/{id}', name: 'app_track_info')]
    public function show(string $id): Response
    {
        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/tracks/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);
        $track = $this->trackFactory->createFromSpotifyData($response->toArray());

        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/recommendations', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
            'query' => [
                'seed_tracks' => $id,
            ],
        ]);
        $recommendations = $this->trackFactory->createMultipleFromSpotifyData($response->toArray()['tracks']);

        return $this->render('track/show.html.twig', [
            'track' => $track,
            'recommendations' => $recommendations,
        ]);
    }

}