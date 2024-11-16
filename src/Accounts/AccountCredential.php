<?php

namespace Codatsoft\Codatbase\Accounts;

use Codatsoft\Codatbase\Network\TNetworkParameters;

interface AccountCredential
{
    public function getGatewayUrl(): string;
    public function getGatewayToken(): string;
    public function getGatewayCode(): string;
    public function getInitialParameters(): TNetworkParameters;

}