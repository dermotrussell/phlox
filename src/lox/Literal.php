<?php

namespace lox;

class Literal extends Expr
{

    private object $value;

    /**
     * @param object $value
     */
    public function __construct(object $value)
    {
        $this->value = $value;
    }

    /**
     * @return object
     */
    public function getValue(): object
    {
        return $this->value;
    }

    public function accept(ExprVisitor $visitor): ExprVisitor
    {
        return $visitor->visitLiteralExpr($this);
    }
}