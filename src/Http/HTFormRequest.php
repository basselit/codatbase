<?php

namespace Codatsoft\Codatbase\Http;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Base for a request on an authenticated route. Resolves the caller's id from the token,
 * never from the payload, and hands the validated input to the DTO through toResult().
 *
 * It carries no notion of "which action is this": the controller method that hints the
 * request already is the route-to-code mapping, and passes the action enum on explicitly
 * where the business layer needs one.
 */
abstract class HTFormRequest extends FormRequest
{

    public int $userId;
    public mixed $validated;

    protected function passedValidation(): void
    {
        $this->validated  = $this->validated();
        $this->userId     = $this->user()->id;
    }

    abstract public function toResult(): HTDReqBase;

}
