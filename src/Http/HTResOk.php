<?php

namespace Codatsoft\Codatbase\Http;

/**
 * The response of an endpoint that has nothing to return but success: { success: true },
 * or { success: false, message } through fail(). Declares no properties on purpose.
 */
final class HTResOk extends HTResBase
{

    public static function ok(?string $message = null): HTResOk
    {
        $res = new self();
        $res->message = $message;

        return $res;
    }

}
