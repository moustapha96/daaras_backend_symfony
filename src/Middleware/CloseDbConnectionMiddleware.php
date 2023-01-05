<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Doctrine\ReopeningEntityManagerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Form\RequestHandlerInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CloseDbConnectionMiddleware implements MiddlewareInterface
{
    public function __construct(private ReopeningEntityManagerInterface $em)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->em->open();
        try {
            return $handler->handle($request);
        } finally {
            $this->em->getConnection()->close();
            $this->em->close();
        }
    }
}
