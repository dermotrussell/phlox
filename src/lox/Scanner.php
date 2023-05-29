<?php

namespace lox;

use Ds\Map;

class Scanner {
    const STR_QUOTE = "\"";
    const STR_EQUAL = "=";
    const STR_LEFT_PAREN = "(";
    const STR_RIGHT_PAREN = ")";
    const STR_LEFT_BRACE = "{";
    const STR_RIGHT_BRACE = "}";
    const STR_SLASH = "/";
    const STR_NEWLINE = "\n";
    const STR_COMMA = ",";
    const STR_DOT = ".";
    const STR_MINUS = "-";
    const STR_PLUS = "+";
    const STR_SEMICOLON = ";";
    const STR_STAR = "*";
    const STR_BANG = "!";
    const STR_LESS_THAN = "<";
    const STR_GREATER_THAN = ">";
    const STR_SPACE = " ";
    const STR_CR = "\r";
    const STR_TAB = "\t";

    private string $source;
    private TokenCollection $tokens;
    private int $start = 0;
    private int $current = 0;
    private int $line = 1;

    private Map $reservedwords;

    /**
     * @param $source
     */
    public function __construct($source) {
        $this->source = $source;
        $this->tokens = new TokenCollection();
        $this->reservedwords = new Map([
            "and" => TokenType::AND,
            "class" => TokenType::_CLASS,
            "else" => TokenType::ELSE,
            "false" => TokenType::FALSE,
            "for" => TokenType::FOR,
            "fun" => TokenType::FUN,
            "if" => TokenType::IF,
            "nil" => TokenType::NIL,
            "or" => TokenType::OR,
            "print" => TokenType::PRINT,
            "return" => TokenType::RETURN,
            "super" => TokenType::SUPER,
            "this" => TokenType::THIS,
            "true" => TokenType::TRUE,
            "var" => TokenType::VAR,
            "while" => TokenType::WHILE
        ]);
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
            case self::STR_LEFT_PAREN: $this->addToken(TokenType::LEFT_PAREN); break;
            case self::STR_RIGHT_PAREN: $this->addToken(TokenType::RIGHT_PAREN); break;
            case self::STR_LEFT_BRACE: $this->addToken(TokenType::LEFT_BRACE); break;
            case self::STR_RIGHT_BRACE: $this->addToken(TokenType::RIGHT_BRACE); break;

            case self::STR_COMMA: $this->addToken(TokenType::COMMA); break;
            case self::STR_DOT: $this->addToken(TokenType::DOT); break;
            case self::STR_MINUS: $this->addToken(TokenType::MINUS); break;
            case self::STR_PLUS: $this->addToken(TokenType::PLUS); break;
            case self::STR_SEMICOLON: $this->addToken(TokenType::SEMICOLON); break;
            case self::STR_STAR: $this->addToken(TokenType::STAR); break;

            case self::STR_BANG: $this->addToken($this->match(self::STR_EQUAL) ? TokenType::BANG_EQUAL : TokenType::BANG); break;
            case self::STR_EQUAL: $this->addToken($this->match(self::STR_EQUAL) ? TokenType::EQUAL_EQUAL : TokenType::EQUAL); break;
            case self::STR_LESS_THAN: $this->addToken($this->match(self::STR_EQUAL) ? TokenType::LESS_EQUAL : TokenType::LESS); break;
            case self::STR_GREATER_THAN: $this->addToken($this->match(self::STR_EQUAL) ? TokenType::GREATER_EQUAL : TokenType::GREATER); break;

            case self::STR_SLASH:
                if ($this->match(self::STR_SLASH)) {
                    // A comment goes until the end of the line
                    while ($this->peek() != self::STR_NEWLINE && !$this->isAtEnd()) $this->advance();
                } else {
                    $this->addToken(TokenType::SLASH);
                }
                break;

            case self::STR_SPACE:
            case self::STR_CR:
            case self::STR_TAB:
                // Ignore whitespace
                break;

            case self::STR_NEWLINE:
                $this->line++; break;

            case self::STR_QUOTE:
                $this->string(); break;

            default:
                if ($this->isDigit($c)) {
                    $this->number();
                } else if ($this->isAlpha($c)) {
                    $this->identifier();
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
        while ($this->isDigit($this->peek()) != null) $this->advance();

        // Look for a fractional part
        if ($this->peek() == self::STR_DOT && $this->isDigit($this->peekNext())) {
            // Consume the "."
            $this->advance();

            while ($this->isDigit($this->peek()) != null) $this->advance();
        }

        $value = substr($this->source, $this->start, $this->current - ($this->start));
        $this->addTokenLiteral(TokenType::NUMBER, (object)$value);
    }

    private function string(): void {
        while ($this->peek() != self::STR_QUOTE && !$this->isAtEnd()) {
            if ($this->peek() == null) $this->line++;
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

    private function peek() : ?string {
        if ($this->isAtEnd()) return null;
        return $this->source[$this->current];
    }

    private function peekNext() : ?string {
        if ($this->current + 1 >= strlen($this->source)) return null;
        return $this->source[$this->current + 1];
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

    private function isAlpha(string $c) : bool {
        return ($c >= "a" && $c <= "z") ||
            ($c >= "A" && $c <= "Z") ||
            $c == "_";
    }

    private function identifier() : void {
        while ($this->isAlphaNumeric($this->peek())) $this->advance();

        $text = substr($this->source, $this->start, $this->current - $this->start);
        $type = $this->reservedwords->get($text, null);

        if ($type == null) $type = TokenType::IDENTIFIER;

        $this->addToken($type);
    }

    private function isAlphaNumeric(?string $c) : bool {
        return $this->isAlpha($c) || $this->isDigit($c);
    }
}