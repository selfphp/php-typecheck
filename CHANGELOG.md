# Changelog

All notable changes to this project will be documented in this file.

This project adheres to [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and follows [Semantic Versioning](https://semver.org/).

## [1.1.0] - 2025-05-28
### Added
- `checkArrayOfType()` for non-throwing validation
- `checkStructure()` for soft structural validation
- `examples/cli-test.php` – CLI showcase for PhpTypeCheck
- Expanded README and example usage

### Fixed
- Minor docblock and type consistency improvements


## [1.0.0] – 2025-05-27
### Added
- `assertArrayOfType()` for strict runtime validation of array element types
- `checkArrayOfType()` for soft validation returning `true`/`false`
- `assertStructure()` for validating associative and nested structures
- `checkStructure()` for soft validation of structured data
- `describeType()` to generate human-readable type strings
- Custom `TypeCheckException` with full type mismatch details
- Optional `?` suffix support for optional keys in schema
- Full PHPUnit test coverage
- PSR-4 autoloading via Composer
- PHPDoc for all public methods

