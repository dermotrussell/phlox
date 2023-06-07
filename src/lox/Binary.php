<?php

namespace lox;

require_once "src/lox/Expr.php";

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

    public function accept(ExprVisitor $visitor): void {
        $visitor->visitBinaryExpr($this);
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

    public function __toString(): string {
        return "";
    }
}