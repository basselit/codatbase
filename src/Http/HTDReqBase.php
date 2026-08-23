<?php

namespace Codatsoft\Codatbase\Http;

use Codatsoft\Codatbase\Base\PActionType;

abstract class HTDReqBase
{
    public int $userId;
    public PActionType $actionType;

    public function __construct(HTFormRequest $request)
    {
        $this->userId = $request->userId;
        $this->actionType = $request->actionType;
    }

}
