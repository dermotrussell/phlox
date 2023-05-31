<?php

namespace lox;

class Assign extends Expr
{
    private Token $name;
    private Expr $value;

    /**
     * @param Token $name
     * @param Expr $value
     */
    public function __construct(Token $name, Expr $value) {
        $this->name = $name;
        $this->value = $value;
    }

    function accept(ExprVisitor $visitor): ExprVisitor {
        return $visitor->visitAssignExpr($this);
    }

    /**
     * @return Token
     */
    public function getName(): Token {
        return $this->name;
    }

    /**
     * @return Expr
     */
    public function getValue(): Expr {
        return $this->value;
    }
}