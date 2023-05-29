<?php

namespace loxinterpreter;

use ArrayIterator;
use Traversable;
use TypeError;

final class TokenCollection implements \ArrayAccess, \IteratorAggregate {

    private array $tokens;

    /**
     * @param array $tokens
     */
    public function __construct(Token ... $tokens) {
        $this->tokens = $tokens;
    }

    public function getIterator(): Traversable {
        return new ArrayIterator($this->tokens);
    }

    public function offsetExists(mixed $offset): bool {
        return isset($this->tokens[$offset]);
    }

    public function offsetGet(mixed $offset): mixed {
        return $this->tokens[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        if ($value instanceof Token) {
            $this->tokens[$offset] = $value;
        }
        else throw new TypeError("Not a token");
    }

    public function add(mixed $value) : void {
        $this->tokens[] = $value;
    }

    public function offsetUnset(mixed $offset): void {
        unset($this->tokens[$offset]);
    }

    public function size(): int {
        return count($this->tokens);
    }
}