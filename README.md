# PhpTypeCheck

**PhpTypeCheck** is a lightweight PHP library for validating runtime types in arrays and nested data structures.  
It helps ensure that incoming data (e.g., from APIs, forms, or dynamic sources) matches expected scalar or object types.

---

## 🚀 Features

- ✅ Validate array elements against scalar or object types
- 🔁 Support for recursive (nested) arrays
- 🧠 Descriptive error messages with exact path
- 🔍 Type inspection via `describeType()`
- 🎯 Framework-agnostic (works in Symfony, Laravel, Slim, or plain PHP)

---

## 📦 Installation

```bash
composer require selfphp/php-typecheck
```

---

## ✨ Basic Usage

### Validate flat arrays
```php
use Selfphp\TypeCheck\TypeChecker;

TypeChecker::assertArrayOfType([1, 2, 3], 'int'); // ✅ OK
TypeChecker::assertArrayOfType(['a', 'b'], 'string'); // ✅ OK
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
// ❌ Throws InvalidArgumentException: Element at [1] is of type string, expected int
```

---

## 🧪 Describe value types

```php
TypeChecker::describeType(42);                     // int
TypeChecker::describeType(['a', 'b']);             // array<string>
TypeChecker::describeType([1, 'x']);               // array<int|string>
TypeChecker::describeType([new User(), new User()]); // array<User>
```

---

## 🛠 Roadmap

- [ ] `assertStructure(array $data, array $schema)` for complex key/type validation
- [ ] Type parser support for `array<string, User>`
- [ ] Optional integration with static analysis tools (Psalm, PHPStan)

---

## 📄 License

MIT © [SELFPHP](https://github.com/selfphp)
