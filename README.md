# fcmb-api-client

[![Latest Stable Version](https://img.shields.io/github/v/release/brokeyourbike/fcmb-api-client-php)](https://github.com/brokeyourbike/fcmb-api-client-php/releases)
[![Total Downloads](https://poser.pugx.org/brokeyourbike/fcmb-api-client/downloads)](https://packagist.org/packages/brokeyourbike/fcmb-api-client)
[![License: MPL-2.0](https://img.shields.io/badge/license-MPL--2.0-purple.svg)](https://github.com/brokeyourbike/fcmb-api-client-php/blob/main/LICENSE)

[![Maintainability](https://api.codeclimate.com/v1/badges/d38ab570bbbdbe2ac34e/maintainability)](https://codeclimate.com/github/brokeyourbike/fcmb-api-client-php/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/d38ab570bbbdbe2ac34e/test_coverage)](https://codeclimate.com/github/brokeyourbike/fcmb-api-client-php/test_coverage)
[![tests](https://github.com/brokeyourbike/polaris-api-client-php/actions/workflows/tests.yml/badge.svg)](https://github.com/brokeyourbike/polaris-api-client-php/actions/workflows/tests.yml)

Polaris Bank API Client for PHP

## Installation

```bash
composer require brokeyourbike/polaris-api-client
```

## Usage

```php
use BrokeYourBike\PolarisBank\Client;
use BrokeYourBike\PolarisBank\Interfaces\ConfigInterface;

assert($config instanceof ConfigInterface);
assert($httpClient instanceof \GuzzleHttp\ClientInterface);
assert($psrCache instanceof \Psr\SimpleCache\CacheInterface);

$apiClient = new Client($config, $httpClient, $psrCache);
$apiClient->fetchAuthTokenRaw();
```

## License
[Mozilla Public License v2.0](https://github.com/brokeyourbike/polaris-api-client-php/blob/main/LICENSE)
