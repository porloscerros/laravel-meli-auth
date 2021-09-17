# [wip] Authorize your Laravel application by a MercadoLibre user.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/porloscerros/laravel-meli.svg?style=flat-square)](https://packagist.org/packages/porloscerros/laravel-meli)
[![Total Downloads](https://img.shields.io/packagist/dt/porloscerros/laravel-meli.svg?style=flat-square)](https://packagist.org/packages/porloscerros/laravel-meli)
![GitHub Actions](https://github.com/porloscerros/laravel-meli/actions/workflows/main.yml/badge.svg)

 

## Installation

You can install the package via composer adding this lines to the `composer.json` file:

```json
{
    "repositories": [
        {
            "url": "https://github.com/porloscerros/laravel-meli-auth.git",
            "type": "git"
        }
    ],
    "require": {
        "porloscerros/laravel-meli-auth": "dev-main"
    }
}
```
And running:
```bash
composer update
```

## Usage

Regiter your application in Mercado Libre API. [Ref](https://developers.mercadolibre.com.ar/es_ar/registra-tu-aplicacion).

Add this keys to the `.env` file:
```dotenv
MELI_CLIENT_ID=
MELI_CLIENT_SECRET=
MELI_REDIRECT_URI=
```

You'll have following methods to Authorize your application by a Mercado Libre User. [Ref](https://developers.mercadolibre.com.ar/es_ar/autenticacion-y-autorizacion#Autenticaci%C3%B3n).
```php
use Porloscerros\Meli\Facades\Meli;

$url = Meli::getAuthorizationUrl(); 
// https://auth.mercadolibre.com.ar/authorization?response_type=code&client_id=MELI_CLIENT_ID&redirect_uri=MELI_REDIRECT_URI
```
After user authorization, when you handle the redirect uri, use the `code` in the url query parameter to get an `access_token`
```php
$code = $request->query('code');
$access_token = Meli::getToken($code);
/*
array (
  'access_token' => 'APP_USR-...',
  'token_type' => 'bearer',
  'expires_in' => 21600,
  'scope' => 'offline_access read write',
  'user_id' => 999999999,
  'refresh_token' => 'TG-...',
)
*/
```

Then you´ll can use the token to do authorized requests. [Ref](https://developers.mercadolibre.com.ar/es_ar/autenticacion-y-autorizacion#Enviar-access-token-por-header).
```php
$meliUser = Meli::me($access_token);
$meliTestUser = Meli::createTestUser($access_token);
$access_token = Meli::refreshToken($access_token['refresh_token']);
```
NOTE: In `$access_token` there is an array, with the same data that the api returned when your application was authorized by the user.


Every time the token is gotten or refreshed, a TokenGotten event will be fired. So, you can handle what you do with the token by adding a Listener for that event.

```php
<?php

namespace App\Listeners\Meli;

use Porloscerros\Meli\Events\TokenGotten;
use Porloscerros\Meli\Facades\Meli;

class SaveCustomerToken
{
    public function handle(TokenGotten $event)
    {
        // do something with the token...
        $data = $event->token;

        $user = Meli::me($data);
        $data['nickname'] = $user['nickname'];
        $data['name'] = "{$user['first_name']} {$user['last_name']}";

        $data['expires'] = now()->addSeconds($event->token['expires_in'])->toDateTimeString();
        unset($data['expires_in']);
        //...
    }
}
```

You will also have a macro available to apply to the Http Client when you go to call the MercadoLibre api
```php
$response = Http::meliClient($access_token)
    ->get($url);
```
This macro will add the headers with the `Bearer token` and `Accept: application/json` header..

### Testing

[wip] Not available yet
```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email porloscerros@gmail.com instead of using the issue tracker.

## Credits

-   [Pablo Pérez](https://github.com/porloscerros)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
