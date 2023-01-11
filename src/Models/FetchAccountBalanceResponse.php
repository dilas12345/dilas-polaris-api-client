<?php

namespace Dilas\PolarisBank\Models;

use BrokeYourBike\DataTransferObject\JsonResponse;

/**
 * @author Che Dilas Yusuph <josephdilas@lovetechnigeria.com.ng>
 */
class FetchAccountBalanceResponse extends JsonResponse
{
    public bool $success;
    public string $errorCode;
    public string $message;
    public ?string $accountNumber;
    public ?string $accountCurrency;
}
