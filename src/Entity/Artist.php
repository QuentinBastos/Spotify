<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
#[ORM\Table(name: 'artist')]
class Artist
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $spotifyUrl;

    #[ORM\Column(type: 'integer', length: 255)]
    private int $followersTotal;

    #[ORM\Column(type: 'json', length: 255)]
    private array $genres;

    #[ORM\Column(type: 'string', length: 255)]
    private string $href;

    #[ORM\Column(type: 'json', length: 255)]
    private array $images;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'integer', length: 255)]
    private int $popularity;

    #[ORM\Column(type: 'string', length: 255)]
    private string $type;

    #[ORM\Column(type: 'string', length: 255)]
    private string $uri;

    public function __construct(
        string $id,
        string $spotifyUrl,
        int $followersTotal,
        array $genres,
        string $href,
        array $images,
        string $name,
        int $popularity,
        string $type,
        string $uri,
    ){
        $this->id = $id;
        $this->spotifyUrl = $spotifyUrl;
        $this->followersTotal = $followersTotal;
        $this->genres = $genres;
        $this->href = $href;
        $this->images = $images;
        $this->name = $name;
        $this->popularity = $popularity;
        $this->type = $type;
        $this->uri = $uri;
    }

    public function getSpotifyUrl(): string
    {
        return $this->spotifyUrl;
    }

    public function setSpotifyUrl(string $spotifyUrl): void
    {
        $this->spotifyUrl = $spotifyUrl;
    }

    public function getFollowersTotal(): int
    {
        return $this->followersTotal;
    }

    public function setFollowersTotal(int $followersTotal): void
    {
        $this->followersTotal = $followersTotal;
    }

    public function getGenres(): array
    {
        return $this->genres;
    }

    public function setGenres(array $genres): void
    {
        $this->genres = $genres;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function setHref(string $href): void
    {
        $this->href = $href;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPopularity(): int
    {
        return $this->popularity;
    }

    public function setPopularity(int $popularity): void
    {
        $this->popularity = $popularity;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }
}