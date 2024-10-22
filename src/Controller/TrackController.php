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

    public function __construct(
        private readonly AuthSpotifyService $authSpotifyService,
        private readonly TrackFactory       $trackFactory
    )
    {
        $this->token = $this->authSpotifyService->auth();
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/', name: 'app_tracks')]
    public function index(Request $request): Response
    {
        $isSubmitted = false;
        $form = $this->createForm(SearchType::class, null, [
            'music' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isSubmitted = true;
            $searchQuery = $form->get('search')->getData();

            $response = $this->authSpotifyService->getTrack($searchQuery, $this->token);
        } else {
            $response = $this->authSpotifyService->getTrackSearchRandom($this->token);
        }
        $tracks = $this->trackFactory->createMultipleFromSpotifyData($response->toArray()['tracks']['items']);

        return $this->render('track/index.html.twig', [
            'tracks' => $tracks,
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
    #[Route('/track/{id}', name: 'app_track_info')]
    public function show(Request $request, ?string $id): Response
    {

        if ($request->query->get('recommendations')) {
            $response = $this->authSpotifyService->getTrackByRecommendations($this->token);
            $recommendations = $this->trackFactory->createMultipleFromSpotifyData($response->toArray()['tracks']);
            return $this->render('recommendation/recommendation.html.twig', [
                'recommendations' => $recommendations,
            ]);
        }


        $response = $this->authSpotifyService->getTrackById($id, $this->token);
        $track = $this->trackFactory->createFromSpotifyData($response->toArray());

        $response = $this->authSpotifyService->getTrackByRecommendations($this->token, $id);
        $recommendations = $this->trackFactory->createMultipleFromSpotifyData($response->toArray()['tracks']);

        return $this->render('track/show.html.twig', [
            'track' => $track,
            'recommendations' => $recommendations,
        ]);
    }
}
