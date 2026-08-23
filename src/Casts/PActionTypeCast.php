<?php

namespace Codatsoft\Codatbase\Casts;
use Codatsoft\Codatbase\Base\PActionType;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class PActionTypeCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?PActionType
    {
        if (is_null($value))
        {
            return null;
        }

        foreach (config('codatbase.action_enums', []) as $actionEnum)
        {
            $case = $actionEnum::tryFrom($value);

            if (!is_null($case))
            {
                return $case;
            }
        }

        return null;
    }

    public function set($model, string $key, $value, array $attributes): ?string
    {
        return $value?->value;
    }

}
