<?php

namespace Dilas\PolarisBank\Models;

use Spatie\DataTransferObject\Attributes\MapFrom;
use BrokeYourBike\DataTransferObject\JsonResponse;

/**
 * @author Che Dilas Yusuph <lovetechnigeria.com.ng>
 */

 class TransactionResponse extends JsonResponse
 {
     #[MapFrom('Status')]
     public string $status;
 
     #[MapFrom('ResponseCode')]
     public string $responseCode;
 
     #[MapFrom('ResponseMessage')]
     public string $responseMessage;
 
     #[MapFrom('Pin')]
     public ?string $pin;
 
     #[MapFrom('AccountNumber')]
     public ?string $accountNumber;
 }
 
