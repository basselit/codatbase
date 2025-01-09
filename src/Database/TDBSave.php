<?php

namespace Codatsoft\Codatbase\Database;

use Illuminate\Support\Facades\DB;

class TDBSave
{
    public static function deleteTableRow(string $table,string $column,string|int $value): int
    {
        if (is_int($value))
        {
            $sql = "delete from " . $table . " where " . $column . " = " . $value;
        } else
        {
            $sql = "delete from " . $table . " where " . $column . " = '" . $value . "'";
        }

        $count = DB::delete($sql);
        return $count;

    }

    public static function updateTableValue(string $table,string $primeCol,int $primeId, string $col,$value)
    {
        $affected = DB::table($table)
            ->where($primeCol, $primeId)
            ->update([$col => $value]);

    }



}
