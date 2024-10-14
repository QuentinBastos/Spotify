<?php

namespace App\Controller;

use App\Factory\ArtistFactory;
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

class ArtistController extends AbstractController
{
    private string $token;
    public ArtistFactory $artistFactory;

    public function __construct(
        private readonly AuthSpotifyService $authSpotifyService,
        private readonly HttpClientInterface $httpClient,
        ArtistFactory $artistFactory
    ) {
        $this->token = $this->authSpotifyService->auth();
        $this->artistFactory = $artistFactory;
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/artist', name: 'app_artist')]
    public function index(Request $request): Response
    {
        $isSubmitted = false;
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isSubmitted = true;
            $searchQuery = $form->get('search')->getData();

            $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/search', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                ],
                'query' => [
                    'q' => $searchQuery,
                    'type' => 'artist',
                    'locale' => 'fr-FR',
                ],
            ]);
            $artists = $this->artistFactory->createMultipleFromSpotifyData($response->toArray()['artists']['items']);
        }
        return $this->render('artist/index.html.twig', [
            'artists' => $artists ?? null,
            'form' => $form->createView(),
            'is_submitted' => $isSubmitted,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/artist/{id}', name: 'app_artist_info')]
    public function show(string $id): Response
    {
        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/artists/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);

        $artist = $this->artistFactory->createFromSpotifyData($response->toArray());

        return $this->render('artist/show.html.twig', [
            'artist' => $artist,
        ]);
    }

}
