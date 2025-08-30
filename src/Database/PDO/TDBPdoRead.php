<?php

namespace Codatsoft\Codatbase\Database\PDO;

use Codatsoft\Codatbase\Database\ITDBReader;
use Codatsoft\Codatbase\Database\TDResponse;

class TDBPdoRead implements ITDBReader
{
    public function readTableFilter(string $table,string $column,string $value): TDResponse
    {
        $ret = new TDResponse();

        return $ret;

    }

}