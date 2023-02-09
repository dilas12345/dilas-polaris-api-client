<?php

namespace Dilas\PolarisBank\Models;

use Spatie\DataTransferObject\Attributes\MapFrom;
use BrokeYourBike\DataTransferObject\JsonResponse;

/**
 * @author Che Dilas Yusuph <lovetechnigeria.com.ng>
 */

 class TransactionResponse extends JsonResponse
 {
    public string $message;
    public ?bool $success;
    public ?string $errorCode;
    public ?string $statusCode;
    public ?string $activityId;

    #[MapFrom('payment.transactionId')]
    public ?string $transactionId;

    #[MapFrom('payment.status')]
    public ?string $transactionStatus;

    #[MapFrom('payment.information')]
    public ?string $transactionInformation;
 }
 
