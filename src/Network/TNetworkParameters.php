<?php

namespace Codatsoft\Codatbase\Network;

use Countable;
use Iterator;

class TNetworkParameters implements Iterator, Countable
{
    public array $elements;
    private int $position;

    public function __construct()
    {
        $this->elements = [];
        $this->position = 0;
    }

    public function addParameter(string $paramTitle, string $paramValue): void
    {
        $newPara = new TNetworkParameter();
        $newPara->parameterTitle = $paramTitle;
        $newPara->parameterValue = $paramValue;
        $this->add($newPara);

    }

    public function add(TNetworkParameter $oneParameter): void
    {
        $this->elements[] = $oneParameter;
    }

    public function current(): TNetworkParameter
    {
        return $this->elements[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->elements[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function count(): int
    {
        return count($this->elements);
    }

}