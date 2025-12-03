# Getting Started with SwiftPHP

## Installation

### Requirements
- PHP 8.1 or higher
- Composer

### Installation via Composer

```bash
composer create-project swiftphp.ma/installer my-app
```

Or clone the repository:

```bash
git clone https://github.com/moaminemahnoudi/swiftphp.git
cd swiftphp
composer install
```

## Configuration

SwiftPHP uses a `.env` file for configuration. Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

Configure your database in `.env`:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=swiftphp
DB_USERNAME=root
DB_PASSWORD=
```

## Your First Route

Open `public/index.php` (or wherever routes are defined in your setup, usually `routes/web.php` if you have one, but in this framework it seems to be `public/index.php` by default).

```php
$router->get('/hello', function() {
    return 'Hello World!';
});
```

## Your First Controller

Generate a controller:

```bash
php bin/swiftphp make:controller HelloController
```

Add a method:

```php
namespace App\Controllers;

use Src\Core\Controller;

class HelloController extends Controller
{
    public function index()
    {
        return "Hello from Controller!";
    }
}
```

Register the route:

```php
use App\Controllers\HelloController;

$router->get('/hello-controller', [HelloController::class, 'index']);
```
