<?php

namespace Dilas\PolarisBank\Enums;

/**
 * @author Che Dilas Yusuph <josephdilas@lovetechnigeria.com.ng>
 */
enum StatusCodeEnum: string
{
    /**
     * Transaction transmit.
     */
    case TRANSMIT = 'TRANSMIT';

    /**
     * Beneficiary payment in progress.
     */
    case IN_PROGRESS = 'IN_PROGRESS';

    /**
     * Transaction paid.
     */
    case PAID = 'PAID';

    /**
     * Transaction has been canceled.
     */
    case CANCELED = 'CANCELLED';

    /**
     * Transaction failed.
     */
    case ERROR = 'ERROR';
}