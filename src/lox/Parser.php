<?php

namespace lox;

class Parser {
    private TokenCollection $tokens;
    private int $current = 0;

    /**
     * @param TokenCollection $tokens
     */
    public function __construct(TokenCollection $tokens) {
        $this->tokens = $tokens;
    }

    public function parse() : ?Expr {
        if ($this->tokens->size() == 0)
            return null;

        try {
            return $this->expression();
        }
        catch (ParseError $e) {
            return null;
        }
    }

    private function expression() : Expr {
        return $this->equality();
    }

    private function equality() : Expr {
        $expr = $this->comparison();

        while ($this->match(TokenType::BANG_EQUAL, TokenType::EQUAL_EQUAL)) {
            $operator = $this->previous();
            $right = $this->comparison();
            $expr = new Binary($expr, $operator, $right);
        }

        return $expr;
    }

    private function match(...$tokenTypes) : bool {
        foreach ($tokenTypes as $tokenType) {
            if ($this->check($tokenType)) {
                $this->advance();
                return true;
            }
        }
        return false;
    }

    private function check(mixed $tokenType) : bool {
        if ($this->isAtEnd()) return false;
        return $this->peek()->getType() == $tokenType;
    }

    private function advance() : Token {
        if (!$this->isAtEnd()) $this->current++;
        return $this->previous();
    }

    private function isAtEnd(): bool {
        return $this->peek()->getType() == TokenType::EOF;
    }

    private function peek() : Token {
        return $this->tokens[$this->current];
    }

    private function previous() : Token {
        return $this->tokens[$this->current - 1];
    }

    private function comparison() : Expr {
        $expr = $this->term();

        while ($this->match(TokenType::GREATER, TokenType::GREATER_EQUAL, TokenType::LESS, TokenType::LESS_EQUAL)) {
            $operator = $this->previous();
            $right = $this->term();
            $expr = new Binary($expr, $operator, $right);
        }

        return $expr;
    }

    private function term() : Expr {
        $expr = $this->factor();

        while ($this->match(TokenType::MINUS, TokenType::PLUS)) {
            $operator = $this->previous();
            $right = $this->factor();
            $expr = new Binary($expr, $operator, $right);
        }

        return $expr;
    }

    private function factor() : Expr {
        $expr = $this->unary();

        while ($this->match(TokenType::SLASH, TokenType::STAR)) {
            $operator = $this->previous();
            $right = $this->unary();
            $expr = new Binary($expr, $operator, $right);
        }

        return $expr;
    }

    private function unary() : Expr {
        if ($this->match(TokenType::BANG, TokenType::MINUS)) {
            $operator = $this->previous();
            $right = $this->unary();

            return new Unary($operator, $right);
        }

        return $this->primary();
    }

    /**
     * @throws ParseError
     */
    private function primary() : ?Expr {
        if ($this->match(TokenType::FALSE)) return new Literal(false);
        if ($this->match(TokenType::TRUE)) return new Literal(true);
        if ($this->match(TokenType::NIL)) return new Literal(null);

        if ($this->match(TokenType::NUMBER, TokenType::STRING)) {
            return new Literal($this->previous()->getLiteral());
        }

        if ($this->match(TokenType::LEFT_PAREN)) {
            $expr = $this->expression();
            $this->consume(TokenType::RIGHT_PAREN, "Expect ')' after expression.");
            return new Grouping($expr);
        }

        $this->error($this->peek(), "Expect expression");
    }

    /**
     * @throws ParseError
     */
    private function consume(TokenType $tokenType, string $message) : ?Token {
        if ($this->check($tokenType)) return $this->advance();

        $this->error($this->peek(), $message);
    }

    /**
     * @throws ParseError
     */
    private function error(Token $token, string $message) : ParseError {
        parse_error($token, $message);

        throw new ParseError();
    }

    private function synchronize() : void {
        $this->advance();

        while (!$this->isAtEnd()) {
            if ($this->previous()->getType() == TokenType::SEMICOLON) return;

            switch ($this->peek()->getType()) {
                case TokenType::_CLASS:case TokenType::FOR:case TokenType::IF:case TokenType::PRINT:
                case TokenType::RETURN:case TokenType::VAR:case TokenType::WHILE:
                    return;
                default:
                    ;
            }
            $this->advance();
        }
    }
}