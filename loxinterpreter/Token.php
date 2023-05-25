<?php

namespace loxinterpreter;

use TokenType;

class Token {
    private TokenType $type;
    private string $lexeme;
    private Object $literal;
    private int $line;

    /**
     * @param TokenType $type
     * @param string $lexeme
     * @param Object $literal
     * @param int $line
     */
    public function __construct(TokenType $type, string $lexeme, object $literal, int $line)
    {
        $this->type = $type;
        $this->lexeme = $lexeme;
        $this->literal = $literal;
        $this->line = $line;
    }

    public function __toString(): string {
        return sprintf("%s %s", $this->type->name, $this->lexeme);
    }
}