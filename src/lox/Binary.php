<?php

namespace lox;

class Binary extends Expr {

    private Expr $left;
    private Token $operator;
    private Expr $right;

    /**
     * @param Expr $left
     * @param Token $operator
     * @param Expr $right
     */
    public function __construct(Expr $left, Token $operator, Expr $right) {
        $this->left = $left;
        $this->operator = $operator;
        $this->right = $right;
    }

    function accept(ExprVisitor $visitor): ExprVisitor {
        return $visitor->visitBinaryExpr($this);
    }

    /**
     * @return Expr
     */
    public function getLeft(): Expr {
        return $this->left;
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