<?php

namespace Dilas\PolarisBank\Interfaces;

interface TransactionInterface
{
    public function getSender(): ?SenderInterface;
    public function getRecipient(): ?RecipientInterface;
    public function getReference(): string;
    public function getRequestSuffix(): int;
    public function getDate(): \Carbon\CarbonInterface;
    public function getSendAmount(): float;
    public function getSendCurrencyCode(): string;
    public function getReceiveAmount(): float;
    public function getReceiveCurrencyCode(): string;
    public function getAccountNumber(): string;
    public function getBankCode(): string;
}
