<?php

namespace Dilas\PolarisBank\Interfaces;

/**
 * @author Che Dilas Yusuph <josephdilas@lovetechnigeria.com.ng>
 */

 interface ConfigInterface
{
    public function getUrl(): string;
    public function getAuthUrl(): string;
    public function getAppId(): string;
    public function getClientId(): string;
    public function getClientSecret(): string;
    public function getResourceId(): string;
    public function getSubscriptionKey(): string;
}
