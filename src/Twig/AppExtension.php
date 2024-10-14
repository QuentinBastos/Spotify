<?php

namespace App\Twig;

use App\Entity\Artist;
use App\Entity\Favorite;
use App\Entity\Track;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security
    )
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_is_favorite', [$this, 'isFavorite']),
        ];
    }

    public function isFavorite(string $entityId, string $entityName): bool
    {
        $trackId = $entityName === Favorite::TYPE_TRACK ? $entityId : null;
        $artistId = $entityName === Favorite::TYPE_ARTIST ? $entityId : null;
        if ($this->security->getUser() instanceof User) {
            $favorite = $this->em->getRepository(Favorite::class)->findOneBy([
                'user' => $this->security->getUser(),
                'track' => $trackId ? $this->em->getRepository(Track::class)->find($trackId) : null,
                'artist' => $artistId ? $this->em->getRepository(Artist::class)->find($artistId) : null,
            ]);
            return (bool)$favorite;
        }
        return false;
    }
}
