<?php

namespace Codatsoft\Codatbase\Http;

use Codatsoft\Codatbase\Base\PActionType;
use Illuminate\Foundation\Http\FormRequest;
use LogicException;

class HTFormRequest extends FormRequest
{

    public int $userId;
    public PActionType $actionType;
    public mixed $validated;

    protected function passedValidation(): void
    {
        $this->validated  = $this->validated();
        $this->userId     = $this->user()->id;
        $this->actionType = $this->resolveActionType();
    }


    protected function resolveActionType(): PActionType
    {
        $name = $this->route()?->getName();

        if (is_null($name))
        {
            throw new LogicException(static::class . ' is used on an unnamed route');
        }

        $routeEnums = config('codatbase.route_enums', []);

        if ($routeEnums === [])
        {
            throw new LogicException('No route enums configured. Set codatbase.route_enums.');
        }

        foreach ($routeEnums as $routeEnum)
        {
            $case = $routeEnum::tryFrom($name);

            if (!is_null($case))
            {
                return $case->action();
            }
        }

        throw new LogicException("No action type mapped for route [{$name}]");
    }

}
