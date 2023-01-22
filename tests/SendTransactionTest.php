<?php

namespace Dilas\PolarisBank\Tests;

use Psr\Http\Message\ResponseInterface;
use Carbon\Carbon;
use Dilas\PolarisBank\Models\TransactionResponse;
use Dilas\PolarisBank\Interfaces\TransactionInterface;
use Dilas\PolarisBank\Interfaces\SenderInterface;
use Dilas\PolarisBank\Interfaces\RecipientInterface;
use Dilas\PolarisBank\Interfaces\ConfigInterface;
use Dilas\PolarisBank\Exceptions\PrepareRequestException;
use Dilas\PolarisBank\Enums\StatusCodeEnum;
use Dilas\PolarisBank\Enums\ErrorCodeEnum;
use Dilas\PolarisBank\Client;

/**
 * @author Che Dilas Yusuph <josephdilas@lovetechnigeria.com.ng>
 */
class SendTransactionTest extends TestCase
{
    private string $clientId = 'unique-clientId';
    private string $clientSecret = 'secure-clientSecret';
    private SenderInterface $sender;
    private RecipientInterface $recipient;

    protected function setUp(): void
    {
        parent::setUp();

        $currentTestDate = Carbon::create(2020, 1, 5, 23, 30, 59);
        Carbon::setTestNow($currentTestDate);

        $this->sender = $this->getMockBuilder(SenderInterface::class)->getMock();
        $this->recipient = $this->getMockBuilder(RecipientInterface::class)->getMock();
    }

    /** @test */
    public function it_will_throw_if_no_sender_in_transaction()
    {
        /** @var TransactionInterface $transaction */
        $transaction = $this->getMockBuilder(TransactionInterface::class)->getMock();

        $this->assertNull($transaction->getSender());

        $mockedConfig = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $mockedClient = $this->getMockBuilder(\GuzzleHttp\ClientInterface::class)->getMock();
        $mockedCache = $this->getMockBuilder(CacheInterface::class)->getMock();

        $this->expectExceptionMessage(SenderInterface::class . ' is required');
        $this->expectException(PrepareRequestException::class);

        /**
         * @var ConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * @var CacheInterface $mockedCache
         * */
        $api = new Client($mockedConfig, $mockedClient, $mockedCache);

        $api->sendTransaction($transaction);
    }

    /** @test */
    public function it_will_throw_if_no_recipient_in_transaction()
    {
        $transaction = $this->getMockBuilder(TransactionInterface::class)->getMock();
        $transaction->method('getSender')->willReturn($this->sender);

        /** @var TransactionInterface $transaction */
        $this->assertNull($transaction->getRecipient());

        $mockedConfig = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $mockedClient = $this->getMockBuilder(\GuzzleHttp\ClientInterface::class)->getMock();
        $mockedCache = $this->getMockBuilder(CacheInterface::class)->getMock();

        $this->expectExceptionMessage(RecipientInterface::class . ' is required');
        $this->expectException(PrepareRequestException::class);

        /**
         * @var ConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * @var CacheInterface $mockedCache
         * */
        $api = new Client($mockedConfig, $mockedClient, $mockedCache);

        $api->sendTransaction($transaction);
    }

    /** @test */
    public function it_can_prepare_request(): void
    {
        $transaction = $this->getMockBuilder(TransactionInterface::class)->getMock();
        $transaction->method('getSender')->willReturn($this->sender);
        $transaction->method('getRecipient')->willReturn($this->recipient);
        $transaction->method('getDate')->willReturn(Carbon::now());

        /** @var TransactionInterface $transaction */
        $this->assertInstanceOf(TransactionInterface::class, $transaction);

        $secretCode = $this->prepareSecretCode($this->clientId, $this->clientSecret);

        $requestId = $this->clientId . Carbon::now()->format('YmdHis') . sprintf('%04d', $transaction->getRequestSuffix());

        $mockedConfig = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $mockedConfig->method('getUrl')->willReturn('https://api.example/');
        $mockedConfig->method('getClientId')->willReturn($this->clientId);
        $mockedConfig->method('getClientSecret')->willReturn($this->clientSecret);

        $mockedResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockedResponse->method('getStatusCode')->willReturn(200);
        $mockedResponse->method('getBody')
            ->willReturn('{
                "Pin": "' . $transaction->getReference() . '",
                "AccountNumber": "' . $transaction->getAccountNumber() . '",
                "Status": "' . StatusCodeEnum::TRANSMIT->value . '",
                "ResponseCode": "' . ErrorCodeEnum::IN_PROGRESS->value . '",
                "ResponseMessage": "Request In Progress"
            }');

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->withArgs([
            'POST',
            'https://api.example/payment/deposit',
            [
                \GuzzleHttp\RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                    'API_KEY' => $this->clientId,
                    'SECRET_CODE' => (string) $secretCode,
                ],
                \GuzzleHttp\RequestOptions::JSON => [
                    'RequestID' => $requestId,
                    'Pin' => $transaction->getReference(),
                    'DateTimeLocal' => (string) Carbon::now()->toISOString(),
                    'DateTimeUTC' => (string) Carbon::now()->setTimezone('UTC')->toISOString(),
                    'TransactionDate' => $transaction->getDate()->toISOString(),
                    'SendAmount' => $transaction->getSendAmount(),
                    'SendAmountCurrency' => $transaction->getSendCurrencyCode(),
                    'ReceiveAmount' => $transaction->getReceiveAmount(),
                    'ReceiveAmountCurrency' => $transaction->getReceiveCurrencyCode(),
                    'AccountNumber' => $transaction->getAccountNumber(),
                    'BankCode' => $transaction->getBankCode(),

                    'SenderFirstName' => $this->sender->getFirstName(),
                    'SenderMiddleName' => $this->sender->getMiddleName(),
                    'SenderLastName' => $this->sender->getLastName(),
                    'SenderAddress' =>'-',
                    'SenderCity' => '-',
                    'SenderState' => '-',
                    'SenderCountry' => $this->sender->getCountryCode(),
                    'SenderPhoneNo' => '-',
                    'SenderZip' => '-',

                    'ReceiverFirstName' => $this->recipient->getFirstName(),
                    'ReceiverMiddleName' => $this->recipient->getMiddleName(),
                    'ReceiverLastName' => $this->recipient->getLastName(),
                    'ReceiverAddress' => '-',
                    'ReceiverCity' => '-',
                    'ReceiverState' => '-',
                    'ReceiverCountry' => $this->recipient->getCountryCode(),
                    'ReceiverPhoneNo' => '-',
                    'ReceiverZip' => '-',
                ],
            ],
        ])->once()->andReturn($mockedResponse);

        /**
         * @var ConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * */
        $api = new Client($mockedConfig, $mockedClient);
        $requestResult = $api->sendTransaction($transaction);

        $this->assertInstanceOf(TransactionResponse::class, $requestResult);
    }
}
