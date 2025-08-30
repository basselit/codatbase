<?php

namespace Codatsoft\Codatbase\Database;

use Codatsoft\Codatbase\Base\TConvertBase;

abstract class TDatabase
{
    protected TDBRead $dbReader;
    protected ?TDBSave $dbSaver;
    protected ?TConvertBase $dbConverter;
    public function __construct(TDBRead $dbReader, ?TDBSave $dbSaver, ?TConvertBase $dbConverter)
    {
        $this->dbConverter = $dbConverter;
        $this->dbReader = $dbReader;
        $this->dbSaver = $dbSaver;

    }

}
