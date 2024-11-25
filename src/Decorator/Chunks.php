<?php

namespace StringObject\Decorator;

use LogicException;
use StringObject\StringObject;

/**
 * @implements \ArrayAccess<int, StringObject>
 * @implements \Iterator<int, StringObject>
 */
class Chunks implements \ArrayAccess, \Countable, \Iterator
{
    private StringObject $strobj;
    private int $length;
    private string $ending;
    private int $index = 0;

    public function __construct(StringObject $strobj, int $length = 76, string $ending = "\r\n")
    {
        $this->strobj = $strobj;
        $this->length = $length;
        $this->ending = $ending;
    }

    public function count(): int
    {
        return (int) \ceil($this->strobj->length() / ($this->length + 0.0));
    }

    public function current(): StringObject
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

    public function offsetGet($index): StringObject
    {
        $offset = $index * $this->length;
        return $this->strobj->substr(
            $offset,
            \min($offset + $this->length, $this->strobj->length() - $offset)
        )->append($this->ending);
    }

    public function offsetSet($offset, $value): void
    {
        throw new LogicException('Cannot assign ' . $value
            . ' to immutable Chunks instance at index ' . $offset);
    }

    public function offsetUnset($offset): void
    {
        throw new LogicException('Cannot unset index ' . $offset . ' on immutable Chunks instance');
    }
}
