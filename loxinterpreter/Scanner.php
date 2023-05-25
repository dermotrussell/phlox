<?php

namespace loxinterpreter;

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

            case " ":
            case "\r":
            case "\t":
                // Ignore whitespace
                break;

            case "\n":
                $this->line++; break;

            case "\"":
                $this->string(); break;

            default:
                if ($this->isDigit($c)) {
                    $this->number();
                }
                else {
                    error($this->line, "Unexpected character: " . $c);
                    break;
                }
        }
    }

    private function isDigit(string $c) : bool {
        return $c[0] > '0' && $c[0] <= '9';
    }

    private function number() : void {
        while ($this->isDigit($this->peek())) $this->advance();

        // Look for a fractional part
        if ($this->peek() == "." && $this->isDigit($this->peekNext())) {
            // Consume the "."
            $this->advance();

            while ($this->isDigit($this->peek())) $this->advance();
        }

        $this->addTokenLiteral(TokenType::NUMBER, );
    }

    private function string(): void {
        while ($this->peek() != "\"" && !$this->isAtEnd()) {
            if ($this->peek() == "\n") $this->line++;
            $this->advance();
        }

        if ($this->isAtEnd()) {
            error($this->line, "Unterminated string.");
            return;
        }

        $this->advance(); // The closing "

        // Trim the surrounding quotes
        $value = substr($this->source, $this->start + 1, $this->current - ($this->start+2));
        $this->addTokenLiteral(TokenType::STRING, (object)$value);
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
        $text = substr($this->source, $this->start, $this->current-$this->start);
        $this->tokens->add(new Token($type, $text, $literal, $this->line));
    }
}