<?php

namespace lox;

abstract class ExprVisitor {
    abstract public function visitAssignExpr(Assign $expr);

    abstract public function visitBinaryExpr(Binary $expr);

    abstract public function visitUnaryExpr(Unary $expr);

    abstract public function visitLiteralExpr(Literal $expr);

    abstract public function visitGroupingExpr(Grouping $grouping);
}