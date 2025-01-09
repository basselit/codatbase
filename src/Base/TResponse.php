<?php

namespace Codatsoft\Codatbase\Base;

use stdClass;

class TResponse
{
    public bool $success = false;
    public string $message = "";
    public int $httpErrorCode = 0;
    public stdClass $data;
    public string $accessToken = "";
    public string $status;
    public int $code;

    public static function from(string $fullError): TResponse
    {
        //$fullError = "successOrError:message:200";
        $me = new self();
        $parts = explode(':',$fullError);
        $me->success = $parts[0] == "success" ? true:false;
        $me->message = $parts[1];
        $me->httpErrorCode = $parts[2];

        return $me;

    }

}
