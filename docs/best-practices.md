# Best Practices

## Code Organization

### Controllers
- Keep controllers thin. Move business logic to Services or Models.
- Use dependency injection to access services.
- Return `Response` objects or `view()` results.

### Models
- Use models for database interaction.
- Define relationships using methods like `hasMany` or `belongsTo`.
- Use `$fillable` to protect against mass assignment vulnerabilities.

### Views
- Keep logic out of views. Use variables passed from controllers.
- Use layouts to share common HTML structures.
- Escape output using `{{ $var }}` (if using a template engine) or `htmlspecialchars` (if using raw PHP).

## Security

### Input Validation
- Always validate user input using `Validator`.
- Never trust data from the client.

### CSRF Protection
- Use `Security::generateCsrfToken()` in forms.
- Verify tokens on POST requests.

### SQL Injection
- Use the Query Builder or ORM which uses PDO parameter binding.
- Avoid raw SQL queries with concatenated strings.

## Performance

- Use eager loading for relationships to avoid N+1 query problems.
- Cache expensive operations.
- Optimize database indexes.
