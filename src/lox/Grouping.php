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

    public function accept(ExprVisitor $visitor): void {
        $visitor->visitGroupingExpr($this);
    }

    /**
     * @return Expr
     */
    public function getExpression(): Expr {
        return $this->expression;
    }

    public function __toString(): string {
        return "TODO";
    }
}