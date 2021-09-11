<?php

namespace Porloscerros\Meli;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Porloscerros\Meli\Events\TokenGetted;

class Meli
{
    protected $client_id;
    protected $client_secret;
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

    public function getAuthorizationUrl()
    {
        $url = config('meli.api.authorization');
        $url = Str::replace('APP_ID', $this->client_id, $url);
        $url = Str::replace('YOUR_URL', $this->redirectUrl, $url);
        return $url;
    }

    /*
     *  Una vez que el usuario inicie sesión será redireccionado a la página de autorización
     * de la aplicación. Allí se le presentarán todos los permisos solicitados.
     *  Otorgados los permisos el usuario será redireccionado al REDIRECT URI configurado en la aplicación con el access token correspondiente:
            https://YOUR_REDIRECT_URI?code=SERVER_GENERATED_AUTHORIZATION_CODE
     */
    public function getToken(string $code)
    {
        $url = config('meli.api.token');
        $data = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'code' => $code,
            'redirect_uri' => $this->redirectUrl,
        ];
        $response = Http::asForm()
            ->accept('application/json')
            ->post($url, $data);
        if ($response->failed()) {
            return $response->throw();
        }
        event(new TokenGetted($response->json()));
        return $response->json();
    }

    /*
     * @param string $refresh_token
     */
    public function refreshToken(string $refresh_token)
    {
        $url = config('meli.api.token');
        $data = [
            'grant_type' => 'refresh_token',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'refresh_token' => $refresh_token,
        ];
        $response = Http::asForm()
            ->accept('application/json')
            ->post($url, $data);
        if ($response->failed()) {
            return $response->throw();
        }
        event(new TokenGetted($response->json()));
        return $response->json();
    }


    /*
     *  Por seguridad, debes enviar el access token por header cada vez que realices llamadas a la API.
            Authorization: Bearer APP_USR-12345678-031820-X-12345678
     *  Y por ejemplo, realizando un GET al recurso /users/me sería:
            curl -H ‘Authorization: Bearer APP_USR-12345678-031820-X-12345678’ \
            https://api.mercadolibre.com/users/me
     */
    public function me(string $access_token)
    {
        $url = config('meli.api.me');
        $response = Http::withToken($access_token)
            ->accept('application/json')
            ->get($url);
        if ($response->failed()) {
            return $response->throw();
        }
        return $response->json();
    }

    public function createTestUser(string $access_token)
    {
        $url = config('meli.api.create_test_user');
        $response = Http::withToken($access_token)
            ->accept('application/json')
            ->post($url, [
                'site_id' => 'MLA',
            ]);
        if ($response->failed()) {
            $response->throw();
        }
        return $response->json();
    }
}
