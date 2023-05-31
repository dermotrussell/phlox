<?php

namespace lox;

abstract class ExprVisitor {
    abstract function visitAssignExpr(Assign $expr);
    abstract function visitBinaryExpr(Binary $expr);
    abstract function visitUnaryExpr(Unary $expr);
    abstract function visitLiteralExpr(Literal $expr);
    abstract function visitGroupingExpr(Grouping $grouping);
}