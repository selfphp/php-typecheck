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
- 🧪 Type inspection via `describeType()`
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
- [ ] Type parser support for `array<string, User>`
- [ ] Optional integration with static analysis tools (Psalm, PHPStan)

---

## 📄 License

MIT ©2025 [SELFPHP](https://github.com/selfphp)
