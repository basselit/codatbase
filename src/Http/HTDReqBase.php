<?php

namespace Codatsoft\Codatbase\Http;

abstract class HTDReqBase
{
    public int $userId;

    public function __construct(HTFormRequest $request)
    {
        $this->userId = $request->userId;
        $this->fill($request->validated);
    }

    /** Copies validated values onto same-named public properties — flat 1:1 keys only. */
    protected function fill(array $validated): void
    {
        foreach ($validated as $key => $value)
        {
            if (str_contains($key, '.')) continue;        // nested rules map to no property
            if (!property_exists($this, $key)) continue;

            $this->$key = $value;
        }
    }

}
