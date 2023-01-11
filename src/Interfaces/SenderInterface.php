<?php

namespace Dilas\PolarisBank\Interfaces;

interface SenderInterface
{
    public function getFirstName(): string;
    public function getMiddleName(): string;
    public function getLastName(): string;
    public function getAddress(): ?string;
    public function getCity(): ?string;
    public function getState(): ?string;

    /**
     * ISO-2 country code
     *
     * @return string
     */
    public function getCountryCode(): string;

    public function getPhoneNumber(): ?string;
    public function getPostalCode(): ?string;
}
