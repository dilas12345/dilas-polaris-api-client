<?php

namespace Dilas\PolarisBank\Interfaces;

/**
 * @author Che Dilas Yusuph <josephdilas@lovetechnigeria.com.ng>
 */
interface BankTransactionInterface
{
    public function getReference(): string;
    public function getAmount(): float;
    public function getCurrencyCode(): string;
    public function getBankCode(): string;
    public function getDebitAccount(): string;
    public function getRecipientAccount(): string;
    public function getRecipientName(): string;
    public function getDescription(): string;
}
