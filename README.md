# SwiftPHP Framework

Modern PHP framework with an expressive router, a simple ORM, built-in authentication, multi-tenant support, middleware, validation, and a lightweight view system. Ships with an AI-friendly CLI to generate controllers, models, and migrations fast — aiming for 70% less boilerplate.

> SwiftPHP targets PHP 8+ and PSR-12 coding style. It’s ideal for small to medium apps, dashboards, APIs, and admin tools.

---

## Features

- Routing: Simple, declarative routes with controller binding
- Controllers: Base controller with dependency injection via attributes
- ORM & Query Builder: Lightweight `Model` + `ModelQuery` with `Database` and `QueryBuilder`
- Migrations: Built-in migrator and generator via CLI
- Authentication: Session-based auth helpers and middleware
- Multi-Tenant: Tenant resolution + role-based access via traits
- Middleware: Auth, CORS, Rate Limiting, Roles, Tenant
- Validation: Declarative validation with `Validator` and exceptions
- Views: PHP templates (`.swift.php`) with layout and components
- CLI: `swiftphp` to generate, migrate, serve, and scaffold resources
- Error Handling: Friendly error template and handler
- Security: Helpers to harden common web risks

---

## Requirements

- PHP 8.1+
- Composer
- PDO extension for your database (e.g., MySQL, SQLite)

---

## Installation

You can install globally (installer) or scaffold directly from source.

### Packagist (recommended)

Once published:

```powershell
composer global require swiftphp.ma/installer
composer create-project swiftphp.ma/installer my-app
```

### From Source (this repository)

```powershell
# Install dependencies
composer install

# Ensure storage directories exist
mkdir storage\views

# Start PHP built-in server
php -S localhost:8000 -t public
```

Open `http://localhost:8000` in your browser.

---

## Quick Start

1. Configure app and database:
   - `config/app.php`
   - `config/database.php`
2. Run migrations:

```powershell
# Windows PowerShell
php bin\swiftphp migrate
```

3. Start the server:

```powershell
php -S localhost:8000 -t public
```

4. Visit `http://localhost:8000` — default `home.swift.php` view renders.

---

## Project Structure

```
app/
  Controllers/       # Application controllers
  Models/            # Domain models (extends src/Core/Model)
config/              # App + DB config
database/migrations/ # Migration files
public/              # Front controller (index.php)
resources/views/     # Views (layouts, components, pages)
src/                 # Framework source (core, router, db, console, etc.)
storage/views/       # Compiled/cached views
```

Key framework modules:
- `src/Core`: Application, Container, Controller, Model, Router
- `src/Http`: Request, Response
- `src/Database`: Database, QueryBuilder
- `src/Auth`: Auth helpers, Tenant
- `src/Middleware`: Middleware base and built-ins
- `src/Validation`: Validator, ValidationException
- `src/View`: View engine
- `src/Console`: CLI application + commands

---

## Routing

Define routes in `public/index.php` using the Router.

Example:

```php
use Src\Core\Application;
use Src\Core\Router;
use App\Controllers\UserController;

$app = new Application();
$router = new Router($app);

$router->get('/', [UserController::class, 'home']);
$router->get('/users', [UserController::class, 'index']);
$router->get('/users/{id}', [UserController::class, 'show']);
$router->post('/users', [UserController::class, 'store']);

$router->dispatch();
```

Route parameters like `{id}` are available via `Request`.

---

## Controllers

Controllers live in `app/Controllers` and extend `Src\Core\Controller`.

```php
namespace App\Controllers;

use Src\Core\Controller;
use Src\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()->get();
        return $this->view('users/index', compact('users'));
    }
}
```

Dependency injection is supported via the container and `Attributes\Inject`.

---

## Models & Querying

Models extend `Src\Core\Model`. A simple `User` model:

```php
namespace App\Models;

use Src\Core\Model;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['name', 'email'];
}
```

Query examples:

```php
$all = User::query()->get();
$one = User::query()->where('id', 1)->first();
User::query()->insert(['name' => 'Ada', 'email' => 'ada@example.com']);
User::query()->update(1, ['name' => 'Ada Lovelace']);
User::query()->delete(1);
```

---

## Migrations

Migration files live in `database/migrations`. Use the CLI to create and run them.

```powershell
# Generate migration
php bin\swiftphp make:migration create_posts_table

# Run migrations
php bin\swiftphp migrate
```

Example migration file name: `2024_01_01_000000_create_users_table.php`.

---

## Views

SwiftPHP uses plain PHP templates with a `.swift.php` suffix.

- Layouts: `resources/views/layouts/app.swift.php`
- Components: `resources/views/components/*.swift.php`
- Pages: `resources/views/*.swift.php`

Render from a controller:

```php
return $this->view('users/index', ['users' => $users]);
```

The engine compiles views to `storage/views` for performance.

---

## Authentication & Authorization

- `src/Auth/Auth.php`: login, logout, current user helpers
- `src/Middleware/AuthMiddleware.php`: gate routes to authenticated users
- `src/Traits/HasRoles.php`: role management on models
- `src/Auth/Tenant.php` + `src/Traits/HasTenant.php`: multi-tenant helpers

Apply middleware on routes or in controller dispatch.

---

## Middleware

Built-ins:
- `AuthMiddleware`
- `CorsMiddleware`
- `RateLimitMiddleware`
- `RoleMiddleware`
- `TenantMiddleware`

Register and apply to routes via the router or a pipeline.

---

## Validation

Use `Src\Validation\Validator` to validate request data.

```php
use Src\Validation\Validator;

Validator::make($request->all(), [
  'email' => ['required', 'email'],
  'name'  => ['required', 'min:2']
])->validate();
```

On failure, throws `ValidationException` which the error handler renders.

---

## CLI Commands

Run via `php bin\\swiftphp` (or `bin\\swiftphp.bat` on Windows):

- `new`: Scaffold a new project
- `serve`: Start a dev server
- `make:controller`: Generate a controller
- `make:model`: Generate a model
- `make:migration`: Generate a migration
- `migrate`: Run migrations
- `help`: Show available commands

Examples:

```powershell
php bin\swiftphp serve
php bin\swiftphp make:controller UserController
php bin\swiftphp make:model User
php bin\swiftphp make:migration add_auth_fields_to_users_table
php bin\swiftphp migrate
```

---

## Configuration

- `config/app.php`: app name, env, debug, timezone, etc.
- `config/database.php`: driver, host, database, user, password

Use environment variables via `src/Support/Env.php`.

---

## Error Handling & Security

- Error pages via `src/Error/error_template.php`
- Central `ErrorHandler` to catch and render
- `src/Security/Security.php` provides helpers for sanitization and headers

---

## Publishing

See `PUBLISHING.md` for full Packagist steps:
- Tag releases (e.g., `v1.0.0`)
- Submit GitHub repo to Packagist
- Optionally configure auto-update hooks

---

## Contributing

Contributions are welcome! Please read `CONTRIBUTING.md` for guidelines.

- Follow PSR-12
- Add tests when possible
- Update `CHANGELOG.md` for user-facing changes

Security issues? See `SECURITY.md` for our policy.

---

## License

MIT. See `LICENSE`.

---

## FAQ

- Where do I define routes?
  - In `public/index.php` using `Router`.
- How do I enable auth-protected pages?
  - Register `AuthMiddleware` on routes or controllers.
- Does it support JSON APIs?
  - Yes. Use `Response` to return JSON and skip views.
- Can I use SQLite?
  - Yes. Configure `config/database.php` for SQLite with PDO.

---

## Credits

Built with care by the SwiftPHP community.
