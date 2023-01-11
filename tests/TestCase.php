<?php

namespace Dilas\PolarisBank\Tests;

use Carbon\Carbon;

/**
 * @author Che Dilas Yusuph <josephdilas@lovetechnigeria.com.ng>
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

    protected function prepareSecretCode(string $username, string $password): string|false
    {
        return openssl_digest($username . Carbon::now()->format('Ymd') . $password, 'SHA256', false);
    }
}