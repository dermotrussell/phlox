<?php

namespace CraftingInterpreters;

use TokenType;

class Scanner {
    private string $source;
    private TokenCollection $tokens;
    private int $start = 0;
    private int $current = 0;
    private int $line = 1;

    /**
     * @param $source
     */
    public function __construct($source) {
        $this->source = $source;
        $this->tokens = new TokenCollection();
    }

    public function scanTokens(): TokenCollection {
        while (!$this->isAtEnd()) {
            // We are at the beginning of the next lexeme
            $this->start = $this->current;
            $this->scanToken();
        }

        $this->tokens->add(new Token(TokenType::EOF, "", (object)NULL, $this->line));
        return $this->tokens;
    }

    private function isAtEnd() : bool {
        return $this->current >= strlen($this->source);
    }

    private function scanToken() : void {
        $c = $this->advance();

        switch ($c) {
            case "(": $this->addToken(TokenType::LEFT_PAREN); break;
            case ")": $this->addToken(TokenType::RIGHT_PAREN); break;
            case "{": $this->addToken(TokenType::LEFT_BRACE); break;
            case "}": $this->addToken(TokenType::RIGHT_BRACE); break;

            case ",": $this->addToken(TokenType::COMMA); break;
            case ".": $this->addToken(TokenType::DOT); break;
            case "-": $this->addToken(TokenType::MINUS); break;
            case "+": $this->addToken(TokenType::PLUS); break;
            case ";": $this->addToken(TokenType::SEMICOLON); break;
            case "*": $this->addToken(TokenType::STAR); break;

            case "!": $this->addToken($this->match("=") ? TokenType::BANG_EQUAL : TokenType::BANG); break;
            case "=": $this->addToken($this->match("=") ? TokenType::EQUAL_EQUAL : TokenType::EQUAL); break;
            case "<": $this->addToken($this->match("=") ? TokenType::LESS_EQUAL : TokenType::LESS); break;
            case ">": $this->addToken($this->match("=") ? TokenType::GREATER_EQUAL : TokenType::GREATER); break;

            case "/":
                if ($this->match("/")) {
                    // A comment goes until the end of the line
                    while ($this->peek() != "\n" && !$this->isAtEnd()) $this->advance();
                } else {
                    $this->addToken(TokenType::SLASH);
                }
                break;

            default:
                error($this->line, "Unexpected character: " . $c); break;
        }
    }

    private function peek() : string {
        if ($this->isAtEnd()) return "\n";
        return $this->source[$this->current];
    }

    private function match(string $expected) : bool {
        if ($this->isAtEnd()) return false;
        if ($this->source[$this->current] != $expected) return false;

        $this->current++;
        return true;
    }

    private function advance() : string {
        return $this->source[$this->current++];
    }

    private function addToken(TokenType $type): void {
        $this->addTokenLiteral($type, (object)null);
    }

    private function addTokenLiteral(TokenType $type, object $literal) : void {
        $text = $this->source.substr($this->start, $this->current-$this->start);
        $this->tokens->add(new Token($type, $text, $literal, $this->line));
    }
}