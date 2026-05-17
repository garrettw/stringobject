# Repository Agent Guide

This repository contains a PHP library for immutable string objects and decorator-based string processing.

## Goals

- Follow repository conventions in `src/` and `spec/`
- Keep the implementation immutable and expressive
- Leave `vendor/` untouched

## Recommended workflow for code changes

1. Inspect the existing implementation in `src/`
2. Review tests or specifications in `spec/`
3. Add or update specs/tests when behavior changes or regressions are fixed
4. Run static analysis and tests before submitting

## Project structure

- `src/` — PHP source code
- `spec/` — PHPSpec behavior-driven specifications
- `vendor/` — dependencies; do not modify directly
- `composer.json` — package metadata and scripts
- `phpspec.yml.dist` — PHPSpec configuration

## Coding conventions

- Preserve existing class and method names unless a clear refactor is required
- Favor immutable value semantics for string objects
- Keep methods and classes small, clear, and well-encapsulated
- Avoid introducing global state or side effects
- Use namespaced PSR-4 structure under `StringObject\`
- Prefer expressive, self-documenting code over dense one-liners

## Testing and quality

- Use `vendor/bin/phpspec` to execute specifications
- Use `vendor/bin/phpstan` for static analysis
- Use `vendor/bin/php-cs-fixer` for formatting if needed
- Run the provided Composer scripts:
  - `composer run test`
  - `composer run phpstan`

## Behavior and compatibility

- Existing public API contracts must remain stable unless the issue explicitly calls for an API change
- Existing string semantics and decorator behavior should continue to work
- When adding new features, keep them consistent with existing API design and style

## File creation and modifications

- Add implementation code to `src/`
- Add behavior specifications to `spec/`
- Do not create unnecessary files outside of the library and tests
- Keep documentation or metadata updates minimal and relevant

## Reviewing changes

- Confirm new or changed behavior with PHPSpec examples
- Confirm there are no PHPStan level 9 issues in `src/`
- Keep diffs concise and focused on the problem being solved
- Explain code changes clearly in the summary and commit messages

## Notes

- `vendor/` is managed by Composer; never edit it manually
- Use the repository Composer scripts instead of third-party wrappers when possible
- If a change is uncertain, prefer safe backward-compatible behavior
- Update this instructions file when new information is discovered that should be documented, such as architecture, tooling, etc.
