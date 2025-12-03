# Code Quality Standards

## Strict Types

All PHP files in the `src/` directory must include:

```php
<?php

declare(strict_types=1);
```

This ensures type safety and catches type-related bugs early.

## PSR-12 Coding Style

We follow PSR-12 coding standards. Run the fixer before committing:

```bash
vendor/bin/php-cs-fixer fix
```

## Static Analysis

We use PHPStan at level 5. Check your code:

```bash
vendor/bin/phpstan analyse
```

## Testing

All new features must include tests:

```bash
vendor/bin/phpunit
```

## Pre-commit Checklist

Before committing:

1. ✅ Run tests: `vendor/bin/phpunit`
2. ✅ Fix code style: `vendor/bin/php-cs-fixer fix`
3. ✅ Check static analysis: `vendor/bin/phpstan analyse`
4. ✅ Update documentation if needed
