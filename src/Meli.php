<?php

namespace Porloscerros\Meli;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Porloscerros\Meli\Events\TokenGotten;

class Meli
{
    /*
     * @var string $client_id
     */
    protected $client_id;
    /*
     * @var string $client_secret
     */
    protected $client_secret;
    /*
     * @var string $redirectUrl
     */
    protected $redirectUrl;

    public function __construct()
    {
        $this->client_id = config('meli.client_id');
        $this->client_secret = config('meli.client_secret');
        $this->redirectUrl = config('meli.redirect_url');
    }

    /*
     * Al iniciar el flujo de autorización, la aplicación que desarrolles deberá redireccionar
     * a Mercado Libre para que los usuarios puedan autenticarse y posteriormente autorizar tu aplicación.
     * En el navegador ingresa la siguiente dirección:
           https://auth.mercadolibre.com.ar/authorization?response_type=code&client_id=$APP_ID&state=$RANDOM_ID&redirect_uri=$REDIRECT_URL
     */
    public function getAuthorizationUrl(string $state = null) :string
    {
        $url = config('meli.api.endpoints.authorization');
        $url = Str::replace('APP_ID', $this->client_id, $url);
        $url = Str::replace('YOUR_URL', $this->redirectUrl, $url);
        $state = $state ??  uniqid();
        $url .= "&state=".$state;
        Cache::put("authorization-url-{$state}", $state, 1200);
        return $url;
    }

    /*
     * Una vez que el usuario inicie sesión será redireccionado a la página de autorización
     * de la aplicación. Allí se le presentarán todos los permisos solicitados.
     * Otorgados los permisos el usuario será redireccionado al REDIRECT URI configurado en la aplicación
     * con el authorization code correspondiente.
     *
     * @param string $code
     */
    public function getToken(string $code, $state = null) :array
    {
        if ($state && !Cache::has("authorization-url-{$state}"))
            throw new \Exception("Meli Auth: The state {$state} in the response does not match the one sent. Ref: https://developers.mercadolibre.com.ar/es_ar/autenticacion-y-autorizacion");
        Cache::forget("authorization-url-{$state}");
        $url = config('meli.api.endpoints.token');
        $data = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'code' => $code,
            'redirect_uri' => $this->redirectUrl,
        ];
        $response = Http::asForm()
            ->accept('application/json')
            ->post($url, $data)
            ->throw();
        event(new TokenGotten($response->json()));
        return $response->json();
    }

    /*
     * @param string $refresh_token
     */
    public function refreshToken(string $refresh_token) :array //recibir array igual que en las demás
    {
        $url = config('meli.api.endpoints.token');
        $data = [
            'grant_type' => 'refresh_token',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'refresh_token' => $refresh_token,
        ];
        $response = Http::asForm()
            ->accept('application/json')
            ->post($url, $data)
            ->throw();
        event(new TokenGotten($response->json()));
        return $response->json();
    }

    /*
     * @param array $access_token
     */
    public function me(array $access_token) :array
    {
        $url = config('meli.api.endpoints.me');
        return Http::meliClient($access_token)
            ->get($url)
            ->throw()
            ->json();
    }

    /*
     * @param array $access_token
     */
    public function createTestUser(array $access_token) :array
    {
        $url = config('meli.api.endpoints.create_test_user');
        return Http::meliClient($access_token)
            ->post($url, [
                'site_id' => 'MLA',
            ])
            ->throw()
            ->json();
    }
}
