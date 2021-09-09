# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/porloscerros/laravel-meli.svg?style=flat-square)](https://packagist.org/packages/porloscerros/laravel-meli)
[![Total Downloads](https://img.shields.io/packagist/dt/porloscerros/laravel-meli.svg?style=flat-square)](https://packagist.org/packages/porloscerros/laravel-meli)
![GitHub Actions](https://github.com/porloscerros/laravel-meli/actions/workflows/main.yml/badge.svg)

Authorize your Laravel application by a MercadoLibre user. 

## Installation

You can install the package via composer adding this lines to the `composer.json` file:

```json
{
    "repositories": [
        {
            "url": "https://github.com/porloscerros/laravel-meli.git",
            "type": "git"
        }
    ],
    "require": {
        "porloscerros/laravel-meli": "dev-main"
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
$access_token = Meli::getToken($code);
```
Then you´ll can use the token to do authorized requests. [Ref](https://developers.mercadolibre.com.ar/es_ar/autenticacion-y-autorizacion#Enviar-access-token-por-header).
```php
use Porloscerros\Meli\Facades\Meli;

$meliUser = Meli::me($access_token);
$meliTestUser = Meli::createTestUser($access_token);
```


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
