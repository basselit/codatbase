<?php

namespace Codatsoft\Codatbase\Base;

interface PAuthUser
{
    public function authId(): int;
    public function authName(): string;
    public function authLocale(): string;

}
