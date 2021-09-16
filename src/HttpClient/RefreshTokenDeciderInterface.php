<?php


namespace Porloscerros\Meli\HttpClient;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RefreshTokenDeciderInterface
{
    public function shouldRefreshToken(
        RequestInterface $request,
        ResponseInterface $response,
        float $sec,
        array $context = [],
        array $config = []
    ): bool;
}