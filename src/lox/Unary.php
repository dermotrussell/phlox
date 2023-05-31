<?php

namespace lox;

class Unary extends Expr {

    private Token $operator;
    private Expr $right;

    /**
     * @param Expr $left
     * @param Token $operator
     * @param Expr $right
     */
    public function __construct(Token $operator, Expr $right) {
        $this->operator = $operator;
        $this->right = $right;
    }

    function accept(ExprVisitor $visitor): ExprVisitor {
        return $visitor->visitUnaryExpr($this);
    }

    /**
     * @return Token
     */
    public function getOperator(): Token {
        return $this->operator;
    }

    /**
     * @return Expr
     */
    public function getRight(): Expr {
        return $this->right;
    }
}