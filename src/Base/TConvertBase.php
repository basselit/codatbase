<?php

namespace Codatsoft\Codatbase\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionObject;
use ReflectionProperty;
use stdClass;

class TConvertBase
{


    protected function fromClassToJson(TModelBase $object): stdClass
    {
        $result = new stdClass();
        $reflectObj = new ReflectionObject($object);

        $properties = $reflectObj->getProperties();

        foreach ($properties as $property)
        {
            $result->{$property->getName()} = $property->getValue($object);
        }

        return $result;

    }


    protected function fromClassToDb(TModelBase $object): Model
    {
        $modelClass = $object->getDbClass();
        $result = new $modelClass();
        $rowExist = false;
        if (isset($object->id) && $object->id != 0)
        {
            $rowExist = true;
        }

        $ref = new ReflectionClass($object);
        $props = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
        $cols = array_column($props,"name");
        $oneUpdate = [];
        foreach ($object as $key => $value)
        {
            $canSave = $this->canSave($key,$value,$result,$cols);
            if ($canSave)
            {
                $dbKey = Str::snake($key);
                if (!$rowExist)
                {
                    $result[$dbKey] = $value;
                } else
                {
                    if ($dbKey != 'id')
                    {
                        $oneUpdate[$dbKey] = $value;
                    }
                }

            }
        }

        //$result->firstOrNew();
        if ($rowExist)
        {
            $result->where('id',$object->id)->update($oneUpdate);
            $result->id = $object->id;
        } else
        {
            $result->save();

        }



        return $result;


    }

    protected function fromDbToAny(Collection|Model|stdClass $db, string $clazz): mixed
    {
        if ($db instanceof Model)
        {
            return $this->fromDbToClass($db,$clazz);
        } elseif ($db instanceof Collection)
        {
            return $this->fromDbToClass($db[0],$clazz);
        } else
        {
            return $this->fromDbToClass($db, $clazz);
        }


    }

    protected function fromDbToClass(Model|stdClass $model, string $objClass): mixed
    {
        $result = new $objClass();
        $reflection = new ReflectionObject($result);
        $properties = $reflection->getProperties();

        if ($model instanceof stdClass)
        {
            $dbArray = (array) $model;
        } else
        {
            $dbArray = $model->toArray();
        }

        foreach ($properties as $property)
        {
            $propName = $property->getName();
            //$dbName = $this->findDBProperty($propName,$dbArray);
            $dbName = $result->getClassColName($propName,$dbArray);

            if ($dbName != '')
            {
                $isObject = isset($result->{$propName}) && is_object($result->{$propName});
                if (!$isObject)
                {
                    $result->{$property->getName()} = $dbArray[$dbName];
                } else
                {
                    $result->{$property->getName()} = $this->processLevelOneChildren($property->getName(),get_class($result->{$propName}),$model[$dbName]);
                }
            }
        }

        return $result;


    }

    private function findDBProperty(string $propName, array $model): string
    {
        $dbName = Str::snake($propName);

        foreach ($model as $key => $value)
        {
            if ($dbName == $key)
            {
                return $key;
            }

            $nonDash = str_replace('_','',$key);
            if ($nonDash == $propName)
            {
                return $key;
            }
        }


        return '';

    }



    protected function fromDbToClassold(Model|stdClass $model, string $objClass): mixed
    {
        $result = new $objClass();
        $reflection = new ReflectionObject($result);
        $properties = $reflection->getProperties();

        foreach ($properties as $property)
        {
            $propName = $property->getName();
            $snake = Str::snake($propName);

            $isObject = isset($result->{$propName}) && is_object($result->{$propName});
            if (!$isObject)
            {
                $result->{$property->getName()} = $model[$snake];
            } else
            {
                $result->{$property->getName()} = $this->processLevelOneChildren($property->getName(),get_class($result->{$propName}),$model[$snake]);
            }
        }

        return $result;


    }

    protected function fromJsonToClass(stdClass $json, string $objClass): mixed
    {
        $result = new $objClass();
        $reflection = new ReflectionObject($result);

        foreach ($json as $key => $value)
        {
           // $this->findPropertyByAll($reflection,$key);
            if ($reflection->hasProperty($key)) {
                $prop = $reflection->getProperty($key);
                $prop->setValue($result, $value);
            }
        }

        return $result;

    }

    private function findPropertyByAll(ReflectionObject $object, $key)
    {
        $allProps = $object->getProperties();
        foreach ($allProps as $oneProp)
        {
            $find1 = $oneProp->name;
            $find2 = $key;
            $findme = 'herello';
        }
    }

    protected function db(mixed $which, string $clazz): mixed
    {
        if ($which instanceof Model)
        {
            return $this->fromDbToClass($which,$clazz);
        } else
        {
            return $this->fromClassToDb($which);
        }

    }

    protected function json(mixed $which, string $clazz = ""): mixed
    {
        if ($which instanceof stdClass)
        {
            return $this->fromJsonToClass($which,$clazz);

        } else
        {
            return $this->fromClassToJson($which);

        }

    }



    //assisted ones
    private function canSave(string $key, $value, Model $model, array $allowedFields): bool
    {
        $dbKey = Str::snake($key);

        if (!($model->getKeyName() == $dbKey && !$model->incrementing) && in_array($key,$allowedFields) && !is_object($value))
        {
            return true;
        } else
        {
            return false;
        }
    }

    private function fromDbLevel2(Model $dbModel, string $clazz): mixed
    {
        $result = new $clazz();
        $reflection = new ReflectionObject($result);
        $properties = $reflection->getProperties();

        foreach ($properties as $property)
        {
            $propName = $property->getName();
            $snake = Str::snake($propName);

            $isObject = isset($result->{$propName}) && is_object($result->{$propName});
            if (!$isObject)
            {
                $result->{$property->getName()} = $dbModel[$snake];
            } else
            {
                $result->{$property->getName()} = $this->processLevelOneChildren2($property->getName(),get_class($result->{$propName}),$dbModel[$snake]);
            }
        }

        return $result;


    }

    protected function processLevelOneChildren(string $propName,string $clazz, mixed $children): mixed
    {
        $collectionClazz = $clazz;
        $singleClazz = rtrim($clazz,"s");
        $col = new $collectionClazz();
        foreach ($children as $child)
        {
            $listItem = $this->fromDbLevel2($child,$singleClazz);
            $col->add($listItem);

        }

        return $col;

    }

    private function processLevelOneChildren2(string $propName,string $clazz, mixed $children)
    {
        $collectionClazz = $clazz;
        $singleClazz = rtrim($clazz,"s");
        $col = new $collectionClazz();
        foreach ($children as $child)
        {
            $listItem = $this->fromDbLevel3($child,$singleClazz);
            $col->add($listItem);

        }

        return $col;

    }

    private function fromDbLevel3(Model $dbModel, string $clazz): mixed
    {
        $result = new $clazz();
        $reflection = new ReflectionObject($result);
        $properties = $reflection->getProperties();

        foreach ($properties as $property)
        {
            $propName = $property->getName();
            $snake = Str::snake($propName);

            $isObject = isset($result->{$propName}) && is_object($result->{$propName});
            if (!$isObject)
            {
                $result->{$property->getName()} = $dbModel[$snake];
            } else
            {
                $result->{$property->getName()} = $this->processLevelOneChildren3($property->getName(),get_class($result->{$propName}),$dbModel[$snake]);
            }
        }

        return $result;


    }

    private function processLevelOneChildren3(string $propName,string $clazz, mixed $children)
    {
        if ($children == null)
        {
            return;
        }

        $collectionClazz = $clazz;
        $singleClazz = rtrim($clazz,"s");
        $col = new $collectionClazz();
        foreach ($children as $child)
        {
            $listItem = $this->fromDbLevel3($child,$singleClazz);
            $col->add($listItem);

        }

        return $col;

    }


}
