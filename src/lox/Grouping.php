<?php

namespace lox;

class Grouping extends Expr {
    private Expr $expression;

    /**
     * @param Expr $expression
     */
    public function __construct(Expr $expression) {
        $this->expression = $expression;
    }

    function accept(ExprVisitor $visitor): ExprVisitor {
        return $visitor->visitGroupingExpr($this);
    }

    /**
     * @return Expr
     */
    public function getExpression(): Expr {
        return $this->expression;
    }
}