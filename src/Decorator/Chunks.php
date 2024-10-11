<?php

namespace StringObject\Decorator;

use StringObject\StringObject;

class Chunks implements \ArrayAccess, \Countable, \Iterator
{
    private $strobj;
    private $length;
    private $ending;
    private $index = 0;

    public function __construct(StringObject $strobj, $length = 76, $ending = "\r\n")
    {
        $this->strobj = $strobj;
        $this->length = $length;
        $this->ending = $ending;
    }

    public function count(): int
    {
        return (int) \ceil($this->strobj->length() / ($this->length + 0.0));
    }

    public function current(): mixed
    {
        return $this->offsetGet($this->index);
    }

    public function key(): mixed
    {
        return $this->index;
    }

    public function next(): void
    {
        $this->index++;
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function valid(): bool
    {
        return ($this->index * $this->length < $this->strobj->length());
    }

    public function offsetExists($index): bool
    {
        $index = (int) $index;
        return ($index >= 0 && $index * $this->length < $this->strobj->length());
    }

    public function offsetGet($index): mixed
    {
        $offset = $index * $this->length;
        return $this->strobj->substr(
            $offset,
            \min($offset + $this->length, $this->strobj->length() - $offset)
        )->append($this->ending);
    }

    public function offsetSet($offset, $value): void
    {
        throw new \LogicException('Cannot assign ' . $value
            . ' to immutable AnyString adapter instance at index ' . $offset);
    }

    public function offsetUnset($offset): void
    {
        throw new \LogicException('Cannot unset index ' . $offset . ' on immutable AnyString adapter instance');
    }
}
