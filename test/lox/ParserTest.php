<?php

namespace lox;

use PHPUnit\Framework\TestCase;

require_once "src/lox/Scanner.php";
require_once "src/lox/Parser.php";
require_once "src/lox/ParseError.php";
require_once "src/lox/TokenCollection.php";
require_once "src/lox/TokenType.php";
require_once "src/lox/Token.php";
require_once "src/lox/Expr.php";
require_once "src/lox/Literal.php";
require_once "src/lox/Lox.php";

class ParserTest extends TestCase {
    /*
     * @test
     */
    public function testParse() : void {
        $source = "1234;  // An integer.";

        $tokens = $this->scan($source);
        $expr = $this->parse($tokens);

        self::assertInstanceOf(Literal::class, $expr);
    }

    protected function scan(string $source): TokenCollection {
        $scanner = new Scanner($source);
        return $scanner->scanTokens();
    }

    protected function parse(TokenCollection $tokens): ?Expr {
        $parser = new Parser($tokens);
        return $parser->parse();
    }
}