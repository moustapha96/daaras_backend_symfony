<?php

declare(strict_types=1);

namespace App\Doctrine;

use Psr\Container\ContainerInterface;

class ReopeningEntityManagerDelegator
{
    public function __invoke(ContainerInterface $container, string $name, callable $createEm): ReopeningEntityManager
    {
        return new ReopeningEntityManager($createEm);
    }
}
