<?php

namespace Dilas\PolarisBank\Interfaces;

/**
 * @author Che Dilas Yusuph <josephdilas@lovetechnigeria.com.ng>
 */

 interface ConfigInterface
{
    public function getUrl(): string;
    public function getClientId(): string;
    public function getClientSecret(): string;
}
