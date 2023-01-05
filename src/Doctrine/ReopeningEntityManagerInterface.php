<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

interface ReopeningEntityManagerInterface extends EntityManagerInterface
{
    public function open(): void;
}
