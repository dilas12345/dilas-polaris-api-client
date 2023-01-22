<?php

namespace Dilas\PolarisBank;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\ClientInterface;
use Carbon\Carbon;
use BrokeYourBike\ResolveUri\ResolveUriTrait;
use BrokeYourBike\HttpEnums\HttpMethodEnum;
use BrokeYourBike\HttpClient\HttpClientTrait;
use BrokeYourBike\HttpClient\HttpClientInterface;
use BrokeYourBike\HasSourceModel\SourceModelInterface;
use BrokeYourBike\HasSourceModel\HasSourceModelTrait;
use BrokeYourBike\HasSourceModel\HasSourceModelInterface;

use Dilas\PolarisBank\Models\TransactionResponse;
use Dilas\PolarisBank\Interfaces\TransactionInterface;
use Dilas\PolarisBank\Interfaces\SenderInterface;
use Dilas\PolarisBank\Interfaces\RecipientInterface;
use Dilas\PolarisBank\Interfaces\ConfigInterface;
use Dilas\PolarisBank\Exceptions\PrepareRequestException;

class Client implements HttpClientInterface, HasSourceModelInterface
{
    use HttpClientTrait;
    use ResolveUriTrait;
    use HasSourceModelTrait;

    private ConfigInterface $config;

    public function __construct(ConfigInterface $config, ClientInterface $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    public function sendTransaction(TransactionInterface $transaction): TransactionResponse
    {
        $sender = $transaction->getSender();
        $recipient = $transaction->getRecipient();

        if (!$sender instanceof SenderInterface) {
            throw PrepareRequestException::noSender($transaction);
        }

        if (!$recipient instanceof RecipientInterface) {
            throw PrepareRequestException::noRecipient($transaction);
        }

        if ($transaction instanceof SourceModelInterface) {
            $this->setSourceModel($transaction);
        }

        $response = $this->performRequest(HttpMethodEnum::POST, 'payment/deposit', [
            'RequestID' => $this->prepareRequestId($transaction),
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

            'SenderFirstName' => $sender->getFirstName(),
            'SenderMiddleName' => $sender->getMiddleName(),
            'SenderLastName' => $sender->getLastName(),
            'SenderAddress' => $sender->getAddress() ?? '-',
            'SenderCity' => $sender->getCity() ?? '-',
            'SenderState' => $sender->getState() ?? '-',
            'SenderCountry' => $sender->getCountryCode(),
            'SenderPhoneNo' => $sender->getPhoneNumber() ?? '-',
            'SenderZip' => $sender->getPostalCode() ?? '-',

            'ReceiverFirstName' => $recipient->getFirstName(),
            'ReceiverMiddleName' => $recipient->getMiddleName(),
            'ReceiverLastName' => $recipient->getLastName(),
            'ReceiverAddress' => $recipient->getAddress() ?? '-',
            'ReceiverCity' => $recipient->getCity() ?? '-',
            'ReceiverState' => $recipient->getState() ?? '-',
            'ReceiverCountry' => $recipient->getCountryCode(),
            'ReceiverPhoneNo' => $recipient->getPhoneNumber() ?? '-',
            'ReceiverZip' => $recipient->getPostalCode() ?? '-',
        ]);

        return new TransactionResponse($response);
    }

    public function getTransactionStatus(TransactionInterface $transaction): TransactionResponse
    {
        if ($transaction instanceof SourceModelInterface) {
            $this->setSourceModel($transaction);
        }

        $response = $this->performRequest(HttpMethodEnum::GET, 'payment/status', [
            'pin' => $transaction->getReference(),
        ]);

        return new TransactionResponse($response);
    }

    /**
     * @param HttpMethodEnum $method
     * @param string $uri
     * @param array<mixed> $data
     * @return ResponseInterface
     *
     * @throws \Exception
     */
    private function performRequest(HttpMethodEnum $method, string $uri, array $data): ResponseInterface
    {
        $option = match($method) {
            HttpMethodEnum::GET => \GuzzleHttp\RequestOptions::QUERY,
            default => \GuzzleHttp\RequestOptions::JSON,
        };

        $options = [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'API_KEY' => $this->config->getClientId(),
                'SECRET_CODE' => $this->prepareSecretCode(),
            ],
            $option => $data,
        ];

        if ($this->getSourceModel()) {
            $options[\BrokeYourBike\HasSourceModel\Enums\RequestOptions::SOURCE_MODEL] = $this->getSourceModel();
        }

        $uri = (string) $this->resolveUriFor($this->config->getUrl(), $uri);
        return $this->httpClient->request($method->value, $uri, $options);
    }

    private function prepareSecretCode(): string
    {
        $secretCodeData = [
            $this->config->getClientId(),
            Carbon::now()->format('Ymd'),
            $this->config->getClientSecret(),
        ];

        return hash('sha256', implode('', $secretCodeData), false);
    }

    private function prepareRequestId(TransactionInterface $transaction): string
    {
        return $this->config->getClientId() .
            Carbon::now()->format('YmdHis') .
            sprintf('%04d', $transaction->getRequestSuffix());
    }
}
