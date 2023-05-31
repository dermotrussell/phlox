<?php

namespace lox;

$GLOBALS['hadError'] = false;

require_once "TokenType.php";
require_once "Token.php";
require_once "TokenCollection.php";
require_once "Scanner.php";

require 'vendor/autoload.php';

/*

global $argc, $argv;

function main(): void
{
    if ($argc == 1) {
        echo "Usage: php Lox.php [script] " . PHP_EOL;
        exit(64);
    } elseif ($argc > 1) {
        runFile($argv[1]);
    } else {
        runPrompt();
    }
}

function runFile($script): void {
    run(file_get_contents($script));
}

function runPrompt(): void {
    while (true) {
        if ($line = readline("> ")) {
            run($line);
            $GLOBALS['hadError'] = false;
        } else {
            break;
        }
    }
}

function run($source): void {
    $scanner = new Scanner($source);
    $tokens = $scanner->scanTokens();

    foreach ($tokens as $token) {
        print($token);
    }
}

*/

function error(int $line, string $message) : void {
    report($line, "", $message);
}

function report(int $line, string $where, string $message) : void {
    print("[line " . $line . "] Error" . $where . ": " . $message . "\n");
    $GLOBALS['hadError'] = true;
}

function parse_error(Token $token, string $message) : void {
    if ($token->getType() == TokenType::EOF) {
        report($token->getLine(), " at end", $message);
    } else {
        report($token->getLine(), " at '" . $token->getLexeme() . "'", $message);
    }

}