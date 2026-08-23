<?php

namespace Codatsoft\Codatbase\Base;

use Countable;
use Iterator;
use JsonSerializable;

abstract class PBases implements PBase, Iterator, Countable, JsonSerializable
{
    public array $elements = [];
    protected int $position = 0;

    public function next(): void { ++$this->position; }
    public function key(): int { return $this->position; }
    public function valid(): bool { return isset($this->elements[$this->position]); }
    public function rewind(): void { $this->position = 0; }
    public function count(): int { return count($this->elements); }
    public function jsonSerialize(): array { return $this->elements; }

}
