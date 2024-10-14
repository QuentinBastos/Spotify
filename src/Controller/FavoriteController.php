<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Entity\Track;
use App\Factory\TrackFactory;
use App\Service\AuthSpotifyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/favorite')]
class FavoriteController extends AbstractController
{
    private string $token;

    public function __construct(
        private readonly AuthSpotifyService     $authSpotifyService,
        private readonly HttpClientInterface    $httpClient,
        private readonly TrackFactory           $trackFactory,
        private readonly EntityManagerInterface $em
    )
    {
        $this->token = $this->authSpotifyService->auth();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/add', name: 'app_favorite_add')]
    public function add(Request $request): JsonResponse
    {
        $trackId = $request->request->get('trackId');
        $user = $this->getUser();
        $track = $this->em->getRepository(Track::class)->find($trackId);

        if (!$track) {
            $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/tracks/' . $trackId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                ],
            ]);
            $track = $this->trackFactory->createFromSpotifyData($response->toArray());
            $this->em->persist($track);
        }

        $favorite = $this->em->getRepository(Favorite::class)->findOneBy([
            'user' => $user,
            'track' => $track,
        ]);

        if ($favorite) {
            $this->addFlash('info', 'Track is already in your favorites.');
            return new JsonResponse(['success' => true]);
        }

        $favorite = new Favorite();
        $favorite->setUser($user);
        $favorite->setTrack($track);
        $this->em->persist($favorite);
        $this->em->flush();

        $this->addFlash('success', 'Track added to favorites!');

        return new JsonResponse(['success' => true]);
    }

    #[Route('/remove', name: 'app_favorite_remove')]
    public function remove(Request $request): JsonResponse
    {
        $trackId = $request->request->get('trackId');
        $user = $this->getUser();
        $track = $this->em->getRepository(Track::class)->find($trackId);

        if (!$track) {
            return new JsonResponse(['success' => false]);
        }

        $favorite = $this->em->getRepository(Favorite::class)->findOneBy([
            'user' => $user,
            'track' => $track,
        ]);

        if (!$favorite) {
            return new JsonResponse(['success' => false]);
        }

        $this->em->remove($favorite);
        $this->em->flush();

        $this->addFlash('success', 'Track removed from favorites!');

        return new JsonResponse(['success' => true]);
    }

    #[Route('/show-track', name: 'app_favorite_show_track')]
    public function showTracks(): Response
    {
        $favoriteTracks = $this->em->getRepository(Favorite::class)->findBy([
            'user' => $this->getUser(),
        ]);
        return $this->render('favorite/show_track.html.twig', [
            'favorite_tracks' => $favoriteTracks,
        ]);
    }

    #[Route('/show-artist', name: 'app_favorite_show_artist')]
    public function showArtist(): Response
    {
        $favoriteArtists = $this->em->getRepository(Favorite::class)->findBy([
            'user' => $this->getUser(),
            'type' => Favorite::TYPE_ARTIST,
        ]);
        return $this->render('favorite/show_artist.html.twig', [
            'favorite_artists' => $favoriteArtists,
        ]);
    }
}
