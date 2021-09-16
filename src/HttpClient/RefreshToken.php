<?php


namespace Porloscerros\Meli\HttpClient;


use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RefreshToken implements RefreshTokenInterface
{
    /*
     * @var RequestInterface $request
     */
    protected $request;
    /*
     * @var ResponseInterface $response
     */
    protected $response;
    /*
     * @var float $sec
     */
    protected $sec;
    /*
     * @var array $context
     */
    protected $context;
    /*
     * @var array $config
     */
    protected $config;

    public function __construct()
    {
        //
    }

    public function refreshToken(
        RequestInterface $request,
        ResponseInterface $response,
        float $sec,
        array $context = [],
        array $config = []
    ): void {
        $this->request = $request;
        $this->response = $response;
        $this->sec = $sec;
        $this->context = $context;
//        $this->config = array_merge(config('http-client-logger'), $config);

        Log::debug('RefreshToken: do the refresh here');
    }
}