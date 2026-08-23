<?php

namespace Codatsoft\Codatbase\Http;


use Codatsoft\Codatbase\Base\PActionType;

interface TRouteAction
{
    public function action(): PActionType;
}
