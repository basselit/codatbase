<?php

namespace Codatsoft\Codatbase\Database;

use Codatsoft\Codatbase\Base\TConvertBase;
use Codatsoft\Codatbase\Database\Eloquent\TDBEloquentRead;
use Codatsoft\Codatbase\Database\PDO\TDBPdoRead;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class TDBRead
{
    private string $mapper;
    protected ITDBReader $reader;

    public function __construct(string $mapper = TDBMappers::ELOQUENT)
    {
        $this->mapper = $mapper;
        if ($this->mapper == TDBMappers::ELOQUENT)
        {
            $this->reader = new TDBEloquentRead();
        } else
        {
            $this->reader = new TDBPdoRead();
        }

    }

    protected function readTableFilter($table,$column,$value): TDResponse
    {
        return $this->reader->readTableFilter($table,$column,$value);
    }

    protected function readTableValue($table,$column,$whereColumn,$columnValue): TDResponse
    {
        return $this->reader->readTableValue($table, $column, $whereColumn, $columnValue);
    }

    public function readTableFilterTwo($table,$column1,$value1,$column2,$value2): TDResponse
    {
        $ret = new TDResponse();
        try {
            $query = DB::table($table);
            $result = $query->where($column1, '=' ,$value1)->where($column2,'=', $value2)->get();
            $ret->success = true;
            $ret->data = $result;
            return $ret;
        } catch (QueryException $exception) {
            $ret->success = false;
            $ret->errorMessage = $exception->getMessage();
            return $ret;
        }
    }


    public function readTableValueTwo($table,$column,$whereColumn1,$columnValue1,$whereColumn2,$columnValue2): TDResponse
    {
        $ret = new TDResponse();
        try {
            $value = DB::table($table)->where($whereColumn1,'=',$columnValue1)->where($whereColumn2,'=',$columnValue2)->value($column);
            if (!$value)
            {
                $ret->success = false;
            } else
            {
                $ret->success = true;
                $ret->data = $value;
            }

            return $ret;
        } catch (QueryException $exception) {
            $ret->success = false;
            $ret->errorMessage = $exception->getMessage();
            return $ret;
        }
    }

    public function readTableFilterSorted($table,$column,$value,$sortColumn1,$sortColumn2): TDResponse
    {
        $ret = new TDResponse();
        try {
            $query = DB::table($table);
            $result = $query->where($column, '=' ,$value)->orderBy($sortColumn1)->orderBy($sortColumn2)->get();
            $ret->success = true;
            $ret->data = $result;
            return $ret;
        } catch (QueryException $exception) {
            $ret->success = false;
            $ret->errorMessage = $exception->getMessage();
            return $ret;
        }
    }

    public function readTableRow($table,$column,$value): TDResponse
    {
        $ret = new TDResponse();
        try {
            $query = DB::table($table);
            $result = $query->where($column, '=' ,$value)->first();
            $ret->success = true;
            $ret->data = $result;
            return $ret;
        } catch (QueryException $exception) {
            $ret->success = false;
            $ret->errorMessage = $exception->getMessage();
            return $ret;
        }
    }

    public function readRawValue(string $theSql, ...$para): TDResponse
    {
        $ret = new TDResponse();
        try {
            $results = DB::select($theSql, $para);
            if (count($results) == 0)
            {
                $ret->data = null;
                $ret->success = true;
                return $ret;
            }

            $ret->success = true;
            $ret->data = $results[0];

            return $ret;

        } catch (QueryException $exception) {
            $ret->success = false;
            $ret->errorMessage = $exception->getMessage();
            return $ret;
        }

    }

    public function getTableCreateSql(string $tableName): string
    {
        $all = DB::select("SHOW CREATE TABLE " . $tableName);
        $oneValue = $all[0];
        $values = (array) $oneValue;
        return $values['Create Table'];

    }

    public function readTable($table): TDResponse
    {
        $ret = new TDResponse();
        try {
            $all = DB::table($table)->get();
            $ret->success = true;
            $ret->data = $all;
            return $ret;
        } catch (QueryException $exception) {
            $ret->success = false;
            $ret->errorMessage = $exception->getMessage();
            return $ret;
        }
    }


}
