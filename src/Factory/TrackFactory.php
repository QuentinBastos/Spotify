<?php

namespace App\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TrackFactory extends AbstractFactory
{

    public function getPriority(): int
    {
        // TODO: Implement getPriority() method.
    }

    public function getKey(): string
    {
        // TODO: Implement getKey() method.
    }

    public function createAuthenticator(ContainerBuilder $container, string $firewallName, array $config, string $userProviderId): string|array
    {
        // TODO: Implement createAuthenticator() method.
    }
}