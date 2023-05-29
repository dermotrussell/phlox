<?php

namespace loxinterpreter;

use PHPUnit\Framework\TestCase;

require_once "loxinterpreter/Scanner.php";
require_once "loxinterpreter/TokenCollection.php";
require_once "loxinterpreter/TokenType.php";
require_once "loxinterpreter/Token.php";

class ScannerTest extends TestCase
{
    /*
     * @test
     */
    public function testComment()
    {
        $scanner = new Scanner("// This is a comment");
        $tokens = $scanner->scanTokens();

        $this->assertEquals(1, $tokens->size());
        $this->assertEquals(\TokenType::EOF, $tokens[0]->getType());
    }
}