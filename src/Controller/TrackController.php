<?php

namespace App\Controller;

use App\Factory\TrackFactory;
use App\Form\SearchType;
use App\Service\SpotifyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TrackController extends AbstractController
{
    private string $token;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function __construct(
        private readonly SpotifyService $spotifyService,
        private readonly TrackFactory   $trackFactory
    )
    {
        $this->token = $this->spotifyService->auth();
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

            $response = $this->spotifyService->getTrack($searchQuery, $this->token);
        } else {
            $response = $this->spotifyService->getTrackSearchRandom($this->token);
        }
        try {
            $tracks = $this->trackFactory->createMultipleFromSpotifyData($response->toArray()['tracks']['items']);
        } catch (ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
        }

        return $this->render('track/index.html.twig', [
            'tracks' => $tracks ?? null,
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
            $response = $this->spotifyService->getTrackByRecommendations($this->token);
            $recommendations = $this->trackFactory->createMultipleFromSpotifyData($response->toArray()['tracks']);
            return $this->render('recommendation/recommendation.html.twig', [
                'recommendations' => $recommendations,
            ]);
        }


        $response = $this->spotifyService->getTrackById($id, $this->token);
        $track = $this->trackFactory->createFromSpotifyData($response->toArray());

        $response = $this->spotifyService->getTrackByRecommendations($this->token, $id);
        $recommendations = $this->trackFactory->createMultipleFromSpotifyData($response->toArray()['tracks']);

        return $this->render('track/show.html.twig', [
            'track' => $track,
            'recommendations' => $recommendations,
        ]);
    }
}
