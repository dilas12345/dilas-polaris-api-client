<?php

namespace Dilas\PolarisBank\Models;

use Spatie\DataTransferObject\Attributes\MapFrom;
use BrokeYourBike\DataTransferObject\JsonResponse;

/**
 * @author Che Dilas Yusuph <josephdilas@lovetechnigeria.com.ng>
 */
class FetchAuthTokenResponse extends JsonResponse
{
    #[MapFrom('expires_in')]
    public int $expiresIn;

    #[MapFrom('access_token')]
    public string $accessToken;
}