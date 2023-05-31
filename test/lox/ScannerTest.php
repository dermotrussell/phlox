<?php

namespace lox;

use PHPUnit\Framework\TestCase;

require_once "src/lox/Scanner.php";
require_once "src/lox/TokenCollection.php";
require_once "src/lox/TokenType.php";
require_once "src/lox/Token.php";

class ScannerTest extends TestCase
{
    /*
     * @test
     */
    public function testComment() : void {
        $source = "// This is a comment";

        $tokens = $this->scan($source);

        $this->assertEquals(1, $tokens->size());
        $this->assertEquals(TokenType::EOF, $tokens[0]->getType());
    }

    /**
     * @param string $source
     * @return TokenCollection
     */
    protected function scan(string $source): TokenCollection {
        $scanner = new Scanner($source);
        return $scanner->scanTokens();
    }

    public function testBooleans() : void {
        $source  = <<<END
        // Booleans
        true;  // Not false.
        false; // Not *not* false.
        END;

        $tokens = $this->scan($source);

        $this->assertEquals(5, $tokens->size());
        $this->assertEquals(TokenType::TRUE, $tokens[0]->getType());
        $this->assertEquals(TokenType::SEMICOLON, $tokens[1]->getType());
        $this->assertEquals(TokenType::FALSE, $tokens[2]->getType());
        $this->assertEquals(TokenType::SEMICOLON, $tokens[3]->getType());
        $this->assertEquals(TokenType::EOF, $tokens[4]->getType());
    }

    public function testNumbers() : void {
        $source  = <<<END
        // Numbers
        1234;  // An integer.
        12.34; // A decimal number.
        END;

        $tokens = $this->scan($source);

        $this->assertEquals(5, $tokens->size());
        $this->assertEquals(TokenType::NUMBER, $tokens[0]->getType());
        $this->assertEquals("1234", $tokens[0]->getLexeme());
        $this->assertEquals(TokenType::SEMICOLON, $tokens[1]->getType());
        $this->assertEquals(TokenType::NUMBER, $tokens[2]->getType());
        $this->assertEquals("12.34", $tokens[2]->getLexeme());
        $this->assertEquals(TokenType::SEMICOLON, $tokens[3]->getType());
        $this->assertEquals(TokenType::EOF, $tokens[4]->getType());
    }

    public function testStrings() : void {
        $source  = <<<END
        // Strings
        "I am a string";
        "";    // The empty string.
        "123"; // This is a string, not a number.
        END;

        $tokens = $this->scan($source);

        $this->assertEquals(7, $tokens->size());
        $this->assertEquals(TokenType::STRING, $tokens[0]->getType());
        $this->assertEquals("\"I am a string\"", $tokens[0]->getLexeme());
        $this->assertEquals(TokenType::SEMICOLON, $tokens[1]->getType());
        $this->assertEquals(TokenType::STRING, $tokens[2]->getType());
        $this->assertEquals("\"\"", $tokens[2]->getLexeme());
        $this->assertEquals(TokenType::SEMICOLON, $tokens[3]->getType());
        $this->assertEquals(TokenType::STRING, $tokens[4]->getType());
        $this->assertEquals("\"123\"", $tokens[4]->getLexeme());
        $this->assertEquals(TokenType::SEMICOLON, $tokens[5]->getType());
        $this->assertEquals(TokenType::EOF, $tokens[6]->getType());
    }

}