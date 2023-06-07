<?php

namespace lox;

abstract class Expr {
    abstract public function accept(ExprVisitor $visitor): void;

    abstract public function __toString(): string;
}
