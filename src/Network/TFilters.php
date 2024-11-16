<?php

namespace Codatsoft\Codatbase\Network;

use Codatsoft\Codatbase\Accounts\AccountCredential;

class TFilters
{
    public array $elements;
    protected AccountCredential $creds;

    public function __construct(AccountCredential $accCredential)
    {
        $this->creds = $accCredential;
        $this->elements = [];
    }

    public function get(int $index): TFilterBase
    {
        return $this->elements[$index];

    }


}
