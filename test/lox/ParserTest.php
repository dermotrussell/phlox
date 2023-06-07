<?php

namespace lox;

use PHPUnit\Framework\TestCase;

require_once "src/lox/Scanner.php";
require_once "src/lox/Parser.php";
require_once "src/lox/ParseError.php";
require_once "src/lox/TokenCollection.php";
require_once "src/lox/TokenType.php";
require_once "src/lox/AstPrinter.php";
require_once "src/lox/Token.php";
require_once "src/lox/Expr.php";
require_once "src/lox/Literal.php";
require_once "src/lox/Binary.php";
require_once "src/lox/Lox.php";

class ParserTest extends TestCase {
    /*
     * @test
     */
    public function testParseInteger() : void {
        $expr = $this->getExpr("1234;  // An integer.");

        self::assertInstanceOf(Literal::class, $expr);

        $print = $this->print($expr);

        $this->assertEquals("1234", $print);
        print($print);
    }

    /*
     * @test
     */
    public function testParseAddExpression() : void {
        $expr = $this->getExpr("23 + 41;");

        $this->assertInstanceOf(Binary::class, $expr);

        $print = $this->print($expr);

        $this->assertEquals("(23+41)", $print);
        print($print);
    }

    protected function scan(string $source): TokenCollection {
        $scanner = new Scanner($source);
        return $scanner->scanTokens();
    }

    protected function parse(TokenCollection $tokens): ?Expr {
        $parser = new Parser($tokens);
        return $parser->parse();
    }

    /**
     * @param string $source
     * @return Expr|null
     */
    private function getExpr(string $source): ?Expr {
        $tokens = $this->scan($source);
        return  $this->parse($tokens);
    }

    /**
     * @param Literal|Expr|null $expr
     * @return string
     */
    public function print(Literal|Expr|null $expr): string {
        $astPrinter = new AstPrinter();
        $astPrinter->print($expr);
        return $astPrinter->getPrint();
    }
}