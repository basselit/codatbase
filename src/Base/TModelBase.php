<?php

namespace Codatsoft\Codatbase\Base;

use Illuminate\Support\Str;

abstract class TModelBase
{
    abstract public function getDbClass(): string;

    public function getMeClass(): string
    {
        return self::class;
    }

    public function getClassColName(string $propName, array $modelArray): string
    {
        $dbName = Str::snake($propName);

        foreach ($modelArray as $key => $value)
        {
            if ($dbName == $key)
            {
                return $key;
            }

            $nonDash = str_replace('_','',$key);
            if ($nonDash == $propName)
            {
                return $key;
            }
        }


        return '';

    }

}