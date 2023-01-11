<?php

namespace Dilas\PolarisBank\Exceptions;

use Dilas\PolarisBank\Interfaces\TransactionInterface;
use Dilas\PolarisBank\Interfaces\SenderInterface;
use Dilas\PolarisBank\Interfaces\RecipientInterface;

/**
 * @author Che Dilas Yusuph <josephdilas@lovetechnigeria.com.ng>
 */
final class PrepareRequestException extends \RuntimeException
{
    private TransactionInterface $transaction;

    protected function __construct(TransactionInterface $transaction, string $message, ?\Throwable $previous = null)
    {
        $this->transaction = $transaction;

        parent::__construct($message, 0, $previous);
    }

    public function getTransaction(): TransactionInterface
    {
        return $this->transaction;
    }

    public static function noSender(TransactionInterface $transaction): self
    {
        $className = $transaction::class;
        $senderClassName = SenderInterface::class;
        return new static($transaction, "{$senderClassName} is required for {$className} `{$transaction->getReference()}`");
    }

    public static function noRecipient(TransactionInterface $transaction): self
    {
        $className = $transaction::class;
        $recipientClassName = RecipientInterface::class;
        return new static($transaction, "{$recipientClassName} is required for {$className} `{$transaction->getReference()}`");
    }
}

