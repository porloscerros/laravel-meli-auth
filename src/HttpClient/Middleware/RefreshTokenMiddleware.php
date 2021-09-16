<?php

namespace Porloscerros\Meli\HttpClient\Middleware;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Porloscerros\Meli\Facades\Meli;
use Porloscerros\Meli\HttpClient\RefreshTokenDeciderInterface;
use Porloscerros\Meli\HttpClient\RefreshTokenInterface;
use Porloscerros\Meli\HttpClient\RetryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RefreshTokenMiddleware
{
    /*
     * @var HttpLoggerInterface $refresher
     */
    protected $refresher;

    /*
     * @var HttpLoggingFilterInterface $filter
     */
    protected $filter;
    /*
     * @var RetryInterface $refresher
     */
    protected $retryier;

    public function __construct(RefreshTokenInterface $refresher, RefreshTokenDeciderInterface $filter, RetryInterface $retryier)
    {
        $this->refresher = $refresher;
        $this->filter = $filter;
        $this->retryier = $retryier;
    }

    /**
     * Called when the middleware is handled by the client.
     *
     * @param array $context
     * @param array $config
     *
     * @return callable
     */
    public function __invoke($context = [], $config = []): callable
    {
        return function (callable $handler) use ($context, $config): callable {
            return function (RequestInterface $request, array $options) use ($context, $config, $handler): PromiseInterface {
                $start = microtime(true);

                $promise = $handler($request, $options);

                return $promise->then(
                    function (ResponseInterface $response) use ($context, $config, $request, $start) {
                        $sec = microtime(true) - $start;
                        if ($this->filter->shouldRefreshToken($request, $response, $sec, $context, $config)) {
                            $context = Meli::refreshToken($context['refresh_token']);
//                            $this->refresher->refreshToken($request, $response, $sec, $context, $config);
                            $res = Http::withToken($context['access_token'])
                                ->accept('application/json');
                            if ($request->getMethod() === 'GET')
                                $res->get($request->getUri())
//                                    ->throw()
                                    ->json();
                            if ($request->getMethod() === 'POST')
                                $res->post( $request->getUri(), json_decode($request->getBody(), true) )
//                                    ->throw()
                                    ->json();
//                            $this->retryier->retry($request, $response, $sec, $context, $config);
                        }

                        return $response;
                    }
                );
            };
        };
    }
}