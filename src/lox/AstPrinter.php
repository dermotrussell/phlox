<?php

namespace lox;

class AstPrinter extends ExprVisitor
{

    /**
     * @param Expr $expr
     * @return string
     */
    public function print(Expr $expr): string {
        return $expr->accept($this);
    }

    public function visitAssignExpr(Assign $expr) {
    }

    public function visitBinaryExpr(Binary $expr) {
        return $this->parenthesize($expr->getOperator()->getLexeme(), $expr->getLeft(), $expr->getRight());
    }

    public function visitUnaryExpr(Unary $expr) {
    }

    public function visitLiteralExpr(Literal $expr) {
    }

    public function visitGroupingExpr(Grouping $grouping) {
    }
}