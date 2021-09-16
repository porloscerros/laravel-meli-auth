<?php


namespace Porloscerros\Meli\HttpClient;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RefreshTokenDecider implements RefreshTokenDeciderInterface
{
    public function shouldRefreshToken(
        RequestInterface $request,
        ResponseInterface $response,
        float $sec,
        array $context = [],
        array $config = []
    ): bool {
        if ($response->getStatusCode() === 401 && $response->getReasonPhrase() === 'Unauthorized') {
            $body = json_decode($response->getBody(), true);
            if (($body['message'] ?? false) === "invalid_token") {
                return true;
            }
        }
        if ($response->getStatusCode() === 403 && $response->getReasonPhrase() === 'Forbidden') {
            return false;
        }
        if (!config('meli.http-client.enabled')) {
            return false;
        }

        if (config('meli.http-client.filter_all')) {
            return true;
        }

        if (config('meli.http-client.filter_2xx') && $response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return true;
        }

        if (config('meli.http-client.filter_3xx') && $response->getStatusCode() >= 300 && $response->getStatusCode() < 400) {
            return true;
        }

        if (config('meli.http-client.filter_4xx') && $response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
            return true;
        }

        if (config('meli.http-client.filter_5xx') && $response->getStatusCode() >= 500 && $response->getStatusCode() < 600) {
            return true;
        }

        if (config('meli.http-client.filter_slow') < $sec) {
            return true;
        }

        return false;
    }
}