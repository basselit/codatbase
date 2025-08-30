<?php

namespace Codatsoft\Codatbase\Database;

use Codatsoft\Codatbase\Database\Eloquent\TDBEloquentRead;
use Codatsoft\Codatbase\Database\Eloquent\TDBEloquentSave;
use Codatsoft\Codatbase\Database\PDO\TDBPdoRead;
use Codatsoft\Codatbase\Database\PDO\TDBPdoSave;
use Illuminate\Support\Facades\DB;

class TDBSave
{
    private string $mapper;
    protected ITDSaver $saver;

    public function __construct(string $mapper = TDBMappers::ELOQUENT)
    {
        $this->mapper = $mapper;
        if ($this->mapper == TDBMappers::ELOQUENT)
        {
            $this->saver = new TDBEloquentSave();
        } else
        {
            $this->saver = new TDBPdoSave();
        }

    }

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
