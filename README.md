# PhpTypeCheck

**PhpTypeCheck** is a lightweight PHP library for validating runtime types in arrays and nested data structures.  
It helps ensure that incoming data (e.g., from APIs, forms, or dynamic sources) matches expected scalar or object types.

---

## 🚀 Features

- ✅ Validate array elements against scalar or object types
- 🔁 Support for recursive (nested) arrays
- 🧩 Validate associative structures with `assertStructure()`
- 🔍 Optional keys with `key?` syntax
- 🧠 Descriptive error messages with full path and actual/expected types
- 🧾 Soft validation via `checkArrayOfType()` and `checkStructure()`
- 🧪 Type inspection via `describeType()`
- 📤 Structured error output with `TypeCheckException::toArray()`
- 🧪 Fully tested with PHPUnit
- 🎯 Framework-agnostic (works in Symfony, Laravel, Slim, or plain PHP)

---

## 📦 Installation

```bash
composer require selfphp/php-typecheck
```

---

## Requirements

- PHP >= 8.1
- Composer

This library uses modern PHP 8.1 features like `readonly` properties.

---

## ✨ Basic Usage

### Validate flat arrays

```php
use Selfphp\PhpTypeCheck\TypeChecker;

TypeChecker::assertArrayOfType([1, 2, 3], 'int');       // ✅ OK
TypeChecker::assertArrayOfType(['a', 'b'], 'string');   // ✅ OK
```

### Validate arrays of objects

```php
TypeChecker::assertArrayOfType([new User(), new User()], User::class); // ✅ OK
```

### Recursive validation

```php
$data = [[1, 2], [3, 4]];
TypeChecker::assertArrayOfType($data, 'int', true); // ✅ OK
```

### Fails with meaningful error

```php
TypeChecker::assertArrayOfType([1, 'two', 3], 'int');
// ❌ Throws TypeCheckException: Element at [1] is of type string, expected int
```

---

## ✅ Soft validation (no exceptions)

### `checkArrayOfType()`

```php
if (!TypeChecker::checkArrayOfType([1, 'two'], 'int')) {
    echo "Invalid array values!";
}
```

### `checkStructure()`

```php
$data = ['email' => 'test@example.com'];
$schema = ['email' => 'string', 'phone?' => 'string'];

if (!TypeChecker::checkStructure($data, $schema)) {
    echo "Invalid structure!";
}
```

---

## 🧩 Validate structured arrays

```php
TypeChecker::assertStructure(
    ['name' => 'Alice', 'age' => 30],
    ['name' => 'string', 'age' => 'int']
);

// with nested structures and optional keys
TypeChecker::assertStructure(
    ['profile' => ['city' => 'Berlin'], 'email' => 'a@example.com'],
    ['profile' => ['city' => 'string'], 'email?' => 'string']
);
```

---

## 📤 Structured error reporting

If a `TypeCheckException` is thrown, you can convert it to a machine-readable format:

```php
try {
    TypeChecker::assertArrayOfType([1, 'x'], 'int');
} catch (TypeCheckException $e) {
    echo json_encode($e->toArray(), JSON_PRETTY_PRINT);
}
```

```json
{
  "message": "Element at [1] is of type string, expected int",
  "path": "1",
  "expected": "int",
  "actual": "string"
}
```

---

## 🧪 Describe value types

```php
TypeChecker::describeType(42);                         // int
TypeChecker::describeType(['a', 'b']);                 // array<string>
TypeChecker::describeType([1, 'x']);                   // array<int|string>
TypeChecker::describeType([new User(), new User()]);  // array<User>
```

---

## 🛠 Roadmap

- [x] `assertStructure(array $data, array $schema)` for complex key/type validation
- [x] `checkArrayOfType()` and `checkStructure()` for soft validation
- [x] Structured error reporting via `toArray()`
- [ ] Type parser support for `array<string, User>`
- [ ] Optional integration with static analysis tools (Psalm, PHPStan)

---

## 📄 License

MIT ©2025 [SELFPHP](https://github.com/selfphp)