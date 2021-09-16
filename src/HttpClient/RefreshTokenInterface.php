<?php


namespace Porloscerros\Meli\HttpClient;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RefreshTokenInterface
{
    public function refreshToken(
        RequestInterface $request,
        ResponseInterface $response,
        float $sec,
        array $context = [],
        array $config = []
    ): void;
}