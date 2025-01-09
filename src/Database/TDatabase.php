<?php

namespace Codatsoft\Codatbase\Database;

use Codatsoft\Codatbase\Base\TConvertBase;

abstract class TDatabase
{
    public function __construct(protected TDBRead $dbReader, protected TDBSave $dbSaver, protected TConvertBase $converter)
    {

    }

    public function reader(): TDBRead
    {
        return $this->dbReader;
    }

    public function saver(): TDBSave
    {
        return $this->dbSaver;

    }

    public function convert(): TConvertBase
    {
        return $this->converter;

    }

}
