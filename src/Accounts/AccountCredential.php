<?php

namespace Codatsoft\Codatbase\Accounts;

use Codatsoft\Codatbase\Network\TNetworkParameters;
use stdClass;

interface AccountCredential
{
    public function getGatewayUrl(): string;
    public function getGatewayToken(): string;
    public function getGatewayCode(): string;
    public function getInitialParameters(): TNetworkParameters;
    public function getExtraHeader(): string;
    public function getAuthValue(): string;
//    public function getAuthUser(): string;

}