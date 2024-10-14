<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Favorite;
use App\Entity\Track;
use App\Factory\ArtistFactory;
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
        private readonly ArtistFactory          $artistFactory,
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
        $entityId = $request->request->get('entityId');
        $entityName = $request->request->get('entityName');
        $track = null;
        $artist = null;

        if ($entityName === Favorite::TYPE_ARTIST) {
            $artist = $this->em->getRepository(Artist::class)->find($entityId);
            if (!$artist) {
                $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/artists/' . $entityId, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                    ],
                ]);

                if ($response->getStatusCode() === 404) {
                    return new JsonResponse(['success' => false, 'message' => 'Artist not found.']);
                }

                $artist = $this->artistFactory->createFromSpotifyData($response->toArray());
                $this->em->persist($artist);
            }
        } elseif ($entityName === Favorite::TYPE_TRACK) {
            $track = $this->em->getRepository(Track::class)->find($entityId);
            if (!$track) {
                $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/tracks/' . $entityId, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                    ],
                ]);

                if ($response->getStatusCode() === 404) {
                    return new JsonResponse(['success' => false, 'message' => $this->token]);
                }

                $track = $this->trackFactory->createFromSpotifyData($response->toArray());
                $this->em->persist($track);
            }
        } else {
            return new JsonResponse(['success' => false, 'message' => 'Invalid entity name.']);
        }

        $user = $this->getUser();

        $favorite = $this->em->getRepository(Favorite::class)->findOneBy([
            'user' => $user,
            'track' => $track ?? null,
            'artist' => $artist ?? null,
        ]);

        if ($favorite) {
            $this->addFlash('info', 'Track is already in your favorites.');
            return new JsonResponse(['success' => true]);
        }

        $favorite = new Favorite();
        $favorite->setUser($user);
        $favorite->setTrack($track);
        $favorite->setArtist($artist);
        $this->em->persist($favorite);
        $this->em->flush();

        $this->addFlash('success', 'Track added to favorites!');

        return new JsonResponse(['success' => true]);
    }

    #[Route('/remove', name: 'app_favorite_remove')]
    public function remove(Request $request): JsonResponse
    {
        $entityId = $request->request->get('entityId');
        $entityName = $request->request->get('entityName');
        $track = null;
        $artist = null;
        $user = $this->getUser();

        if ($entityName === Favorite::TYPE_ARTIST) {
            $artist = $this->em->getRepository(Artist::class)->find($entityId);
            if (!$artist) {
                return new JsonResponse(['success' => false, 'message' => 'Artist not found.']);
            }
        } elseif ($entityName === Favorite::TYPE_TRACK) {
            $track = $this->em->getRepository(Track::class)->find($entityId);
            if (!$track) {
                return new JsonResponse(['success' => false, 'message' => 'Track not found.']);
            }
        } else {
            return new JsonResponse(['success' => false, 'message' => 'Invalid entity name.']);
        }

        $favorite = $this->em->getRepository(Favorite::class)->findOneBy([
            'user' => $user,
            'track' => $track ?? null,
            'artist' => $artist ?? null,
        ]);

        if (!$favorite) {
            return new JsonResponse(['success' => false]);
        }

        $this->em->remove($favorite);
        $this->em->flush();

        $this->addFlash('success', 'Favorite removed successfully!');

        return new JsonResponse(['success' => true]);
    }

    #[Route('/show-track', name: 'app_favorite_show_track')]
    public function showTracks(): Response
    {
        $favoriteTracks = $this->em->getRepository(Favorite::class)->findBy([
            'user' => $this->getUser(),
            'artist' => null,
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
            'track' => null,
        ]);
        return $this->render('favorite/show_artist.html.twig', [
            'favorite_artists' => $favoriteArtists,
        ]);
    }
}
