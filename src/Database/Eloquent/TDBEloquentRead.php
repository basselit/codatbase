<?php

namespace Codatsoft\Codatbase\Database\Eloquent;

use Codatsoft\Codatbase\Database\ITDBReader;
use Codatsoft\Codatbase\Database\TDResponse;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class TDBEloquentRead implements ITDBReader
{

    public function readTableFilter($table,$column,$value): TDResponse
    {
        $ret = new TDResponse();
        try {
            $query = DB::table($table);
            $result = $query->where($column, '=' ,$value)->get();
            $ret->success = true;
            $ret->data = $result->all();
            return $ret;
        } catch (QueryException $exception) {
            $ret->success = false;
            $ret->errorMessage = $exception->getMessage();
            return $ret;
        }
    }

    public function readTableValue($table,$column,$whereColumn,$columnValue): TDResponse
    {
        $ret = new TDResponse();
        try {
            $value = DB::table($table)->where($whereColumn,'=',$columnValue)->value($column);
            $ret->success = true;
            $ret->data = $value;
            return $ret;
        } catch (QueryException $exception) {
            $ret->success = false;
            $ret->errorMessage = $exception->getMessage();
            return $ret;
        }
    }



}