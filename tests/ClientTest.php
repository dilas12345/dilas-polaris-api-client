<?php

namespace Dilas\PolarisBank\Tests;

use PHPUnit\Framework\TestCase;
use BrokeYourBike\ResolveUri\ResolveUriTrait;
use BrokeYourBike\HttpClient\HttpClientTrait;
use BrokeYourBike\HttpClient\HttpClientInterface;
use BrokeYourBike\HasSourceModel\HasSourceModelTrait;
use BrokeYourBike\HasSourceModel\HasSourceModelInterface;
use Dilas\PolarisBank\Interfaces\ConfigInterface;
use Dilas\PolarisBank\Client;

/**
 * @author Che Dilas Yusuph <josephdilas@lovetechnigeria.com.ng>
 */
class ClientTest extends TestCase
{
    /** @test */
    public function it_implements_http_client_interface(): void
    {
        /** @var ConfigInterface */
        $mockedConfig = $this->getMockBuilder(ConfigInterface::class)->getMock();

        /** @var \GuzzleHttp\ClientInterface */
        $mockedHttpClient = $this->getMockBuilder(\GuzzleHttp\ClientInterface::class)->getMock();

        $api = new Client($mockedConfig, $mockedHttpClient);

        $this->assertInstanceOf(HttpClientInterface::class, $api);
        $this->assertSame($mockedConfig, $api->getConfig());
    }

    /** @test */
    public function it_implements_has_source_model_interface(): void
    {
        /** @var ConfigInterface */
        $mockedConfig = $this->getMockBuilder(ConfigInterface::class)->getMock();

        /** @var \GuzzleHttp\ClientInterface */
        $mockedHttpClient = $this->getMockBuilder(\GuzzleHttp\ClientInterface::class)->getMock();

        $api = new Client($mockedConfig, $mockedHttpClient);

        $this->assertInstanceOf(HasSourceModelInterface::class, $api);
    }

    /** @test */
    public function it_uses_http_client_trait(): void
    {
        $usedTraits = class_uses(Client::class);

        $this->assertArrayHasKey(HttpClientTrait::class, $usedTraits);
    }

    /** @test */
    public function it_uses_resolve_uri_trait(): void
    {
        $usedTraits = class_uses(Client::class);

        $this->assertArrayHasKey(ResolveUriTrait::class, $usedTraits);
    }

    /** @test */
    public function it_uses_has_source_model_trait(): void
    {
        $usedTraits = class_uses(Client::class);

        $this->assertArrayHasKey(HasSourceModelTrait::class, $usedTraits);
    }
}
