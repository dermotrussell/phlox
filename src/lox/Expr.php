<?php

namespace lox;

abstract class Expr {
    abstract function accept(ExprVisitor $visitor) : ExprVisitor;
}
