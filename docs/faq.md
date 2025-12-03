# Frequently Asked Questions

## General

### What is SwiftPHP?
SwiftPHP is a modern, lightweight PHP framework designed for speed and simplicity.

### Is it production-ready?
Yes, SwiftPHP is built with stability and security in mind. However, as with any framework, ensure you follow security best practices.

## Technical

### How do I enable debug mode?
Set `APP_DEBUG=true` in your `.env` file.

### How do I run migrations?
Run `php bin/swiftphp migrate` in your terminal.

### Can I use a different database?
SwiftPHP supports MySQL and SQLite out of the box via PDO. You can configure the connection in `.env`.

### How do I add a new route?
Open `public/index.php` and add your route using `$router->get()`, `$router->post()`, etc.

## Troubleshooting

### "Class not found" error
Run `composer dump-autoload` to regenerate the autoloader class map.

### "Permission denied" error
Ensure the `storage` directory is writable by the web server user.
