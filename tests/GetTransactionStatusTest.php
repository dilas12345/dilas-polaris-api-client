<?php

namespace Dilas\PolarisBank\Tests;

use Psr\Http\Message\ResponseInterface;
use Carbon\Carbon;
use Dilas\PolarisBank\Models\TransactionResponse;
use Dilas\PolarisBank\Interfaces\TransactionInterface;
use Dilas\PolarisBank\Interfaces\ConfigInterface;
use Dilas\PolarisBank\Enums\StatusCodeEnum;
use Dilas\PolarisBank\Enums\ErrorCodeEnum;
use Dilas\PolarisBank\Client;

/**
 * @author Che Dilas Yusuph <josephdilas@lovetechnigeria.com.ng>
 */

 class GetTransactionStatusTest extends TestCase
 {
     private string $clientId = 'client-id';
     private string $clientSecret = 'client-secret';
     private string $reference = 'REF-123';
 
     protected function setUp(): void
     {
         parent::setUp();
 
         $currentTestDate = Carbon::create(2020, 1, 5, 23, 30, 59);
         Carbon::setTestNow($currentTestDate);
     }
 
     /** @test */
     public function it_can_prepare_request(): void
     {
         $transaction = $this->getMockBuilder(TransactionInterface::class)->getMock();
         $transaction->method('getReference')->willReturn($this->reference);
 
         /** @var TransactionInterface $transaction */
         $this->assertInstanceOf(TransactionInterface::class, $transaction);
 
         $secretCode = $this->prepareSecretCode($this->clientId, $this->clientSecret);
 
         $mockedConfig = $this->getMockBuilder(ConfigInterface::class)->getMock();
         $mockedConfig->method('getUrl')->willReturn('https://api.example/');
         $mockedConfig->method('getClientId')->willReturn($this->clientId);
         $mockedConfig->method('getClientSecret')->willReturn($this->clientSecret);
 
         $mockedResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
         $mockedResponse->method('getStatusCode')->willReturn(200);
         $mockedResponse->method('getBody')
             ->willReturn('{
                 "Pin": "' . $this->reference . '",
                 "AccountNumber": "13465798",
                 "Status": "' . StatusCodeEnum::TRANSMIT->value . '",
                 "ResponseCode": "' . ErrorCodeEnum::IN_PROGRESS->value . '",
                 "ResponseMessage": "Request In Progress"
             }');
 
         /** @var \Mockery\MockInterface $mockedClient */
         $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
         $mockedClient->shouldReceive('request')->withArgs([
             'GET',
             'https://api.example/payment/status',
             [
                 \GuzzleHttp\RequestOptions::HEADERS => [
                     'Accept' => 'application/json',
                     'API_KEY' => $this->clientId,
                     'SECRET_CODE' => (string) $secretCode,
                 ],
                 \GuzzleHttp\RequestOptions::QUERY => [
                     'pin' => $this->reference,
                 ],
             ],
         ])->once()->andReturn($mockedResponse);
 
         /**
          * @var ConfigInterface $mockedConfig
          * @var \GuzzleHttp\Client $mockedClient
          * */
         $api = new Client($mockedConfig, $mockedClient);
         $requestResult = $api->getTransactionStatus($transaction);
 
         $this->assertInstanceOf(TransactionResponse::class, $requestResult);
     }
 }
 