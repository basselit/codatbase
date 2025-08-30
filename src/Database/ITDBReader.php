<?php

namespace Codatsoft\Codatbase\Database;

interface ITDBReader
{
    function readTableFilter(string $table,string $column,string $value): TDResponse;

}