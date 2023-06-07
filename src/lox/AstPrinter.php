<?php

namespace lox;

require_once "src/lox/Expr.php";
require_once "src/lox/ExprVisitor.php";

class AstPrinter extends ExprVisitor
{
    private string $print;

    /**
     * @return string
     */
    public function getPrint(): string {
        return $this->print;
    }

    public function __construct() {
        $this->print = "";
    }

    /**
     * @param Expr $expr
     * @return string
     */
    public function print(Expr $expr): string {
        $visitor = $expr->accept($this);

        return "";
    }

    public function visitAssignExpr(Assign $expr) {
    }

    public function visitBinaryExpr(Binary $expr) {
        $this->print .= $this->parenthesize($expr->getOperator()->getLexeme(), $expr->getLeft(), $expr->getRight());
    }

    public function visitUnaryExpr(Unary $expr) {
    }

    public function visitLiteralExpr(Literal $expr): void {
        $value = $expr->getValue();
        $this->print .= "" . $value->scalar;
    }

    public function visitGroupingExpr(Grouping $grouping) {
    }

    private function parenthesize(string $lexeme, Expr $left, Expr $right) : string {
        return "(" . $left . $lexeme . $right . ")";
    }
}