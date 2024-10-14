<?php

namespace App\Twig;

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

    public function isFavorite(string $trackId): bool
    {
        $track = $this->em->getRepository(Track::class)->find($trackId);
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return false;
        }
        $favorite = $this->em->getRepository(Favorite::class)->findOneBy([
            'track' => $track,
            'user' => $user
        ]);
        return $favorite !== null;
    }
}
