<?php

require __DIR__ . '/../vendor/autoload.php';

use Selfphp\PhpTypeCheck\TypeChecker;
use Selfphp\PhpTypeCheck\Exception\TypeCheckException;

class User {}

function printResult(string $label, bool $success, ?string $error = null): void {
    $emoji = $success ? "✅" : "❌";
    echo "$emoji $label";
    if (!$success && $error) {
        echo " - $error";
    }
    echo PHP_EOL;
}

echo "Running PhpTypeCheck CLI Demo...\n\n";

// === Test 1: assertArrayOfType flat ===
try {
    TypeChecker::assertArrayOfType(['a', 'b'], 'string');
    printResult("assertArrayOfType (flat string array)", true);
} catch (TypeCheckException $e) {
    printResult("assertArrayOfType (flat string array)", false, $e->getMessage());
}

// === Test 2: assertArrayOfType with objects ===
try {
    TypeChecker::assertArrayOfType([new User(), new User()], User::class);
    printResult("assertArrayOfType (object array)", true);
} catch (TypeCheckException $e) {
    printResult("assertArrayOfType (object array)", false, $e->getMessage());
}

// === Test 3: assertArrayOfType recursive ===
try {
    TypeChecker::assertArrayOfType([[1, 2], [3, 4]], 'int', true);
    printResult("assertArrayOfType (recursive int array)", true);
} catch (TypeCheckException $e) {
    printResult("assertArrayOfType (recursive int array)", false, $e->getMessage());
}

// === Test 4: assertStructure optional ===
try {
    TypeChecker::assertStructure(
        ['email' => 'a@example.com'],
        ['email' => 'string', 'phone?' => 'string']
    );
    printResult("assertStructure with optional field", true);
} catch (TypeCheckException $e) {
    printResult("assertStructure with optional field", false, $e->getMessage());
}

// === Test 5: assertStructure missing required ===
try {
    TypeChecker::assertStructure(['name' => 'Alice'], ['name' => 'string', 'age' => 'int']);
    printResult("assertStructure missing required", false);
} catch (TypeCheckException $e) {
    printResult("Missing required key caught", true, $e->getMessage());
}

// === Test 6: assertArrayOfType wrong type ===
try {
    TypeChecker::assertArrayOfType([1, 'fail', 3], 'int');
    printResult("assertArrayOfType wrong type", false);
} catch (TypeCheckException $e) {
    printResult("Wrong type in array caught", true, $e->getMessage());
}

// === Test 7: describeType ===
echo "\nDescribe types:\n";
echo "int:             " . TypeChecker::describeType(42) . PHP_EOL;
echo "array<string>:   " . TypeChecker::describeType(['a', 'b']) . PHP_EOL;
echo "array<int|string>: " . TypeChecker::describeType([1, 'x']) . PHP_EOL;
echo "array<User>:     " . TypeChecker::describeType([new User(), new User()]) . PHP_EOL;

// === Test 8: checkArrayOfType ===
echo "\ncheckArrayOfType:\n";
echo "ints valid:      " . (TypeChecker::checkArrayOfType([1, 2, 3], 'int') ? '✅' : '❌') . PHP_EOL;
echo "invalid types:   " . (TypeChecker::checkArrayOfType([1, 'fail'], 'int') ? '✅' : '❌') . PHP_EOL;

// === Test 9: checkStructure ===
echo "\ncheckStructure:\n";
$validData = ['name' => 'Alice', 'age' => 30];
$invalidData = ['name' => 'Alice'];
$schema = ['name' => 'string', 'age' => 'int'];

echo "valid:           " . (TypeChecker::checkStructure($validData, $schema) ? '✅' : '❌') . PHP_EOL;
echo "missing key:     " . (TypeChecker::checkStructure($invalidData, $schema) ? '✅' : '❌') . PHP_EOL;
