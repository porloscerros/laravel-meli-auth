<?php


namespace Porloscerros\Meli\HttpClient;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RetryInterface
{
    public function retry(
        RequestInterface $request,
        ResponseInterface $response,
        float $sec,
        array $context = [],
        array $config = []
    ): void;
}