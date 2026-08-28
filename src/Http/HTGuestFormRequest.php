<?php

namespace Codatsoft\Codatbase\Http;

use Illuminate\Foundation\Http\FormRequest;

abstract class HTGuestFormRequest extends FormRequest
{
    public mixed $validated;

    protected function passedValidation(): void
    {
        // No user and no action type: guest routes have neither an authenticated
        // identity nor a place in the buyer/seller action pipeline.
        $this->validated = $this->validated();
    }

    abstract public function toResult(): HTDReqGuestBase;

}