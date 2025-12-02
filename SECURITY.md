# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | :white_check_mark: |
| < 1.0   | :x:                |

## Reporting a Vulnerability

We take security vulnerabilities seriously. If you discover a security issue, please follow these steps:

### 1. Do Not Disclose Publicly

Please do not open a public GitHub issue for security vulnerabilities.

### 2. Report Privately

Send an email to: **security@swiftphp.dev** with:

- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (if any)
- Your contact information

### 3. Response Timeline

- We will acknowledge your report within 48 hours
- We will provide a detailed response within 7 days
- We will work on a fix and keep you updated
- Once fixed, we will credit you in the release notes (unless you prefer to remain anonymous)

## Security Best Practices

When using SwiftPHP in production:

### 1. Environment Configuration
```env
APP_ENV=production
APP_DEBUG=false
```

### 2. Database Security
- Use prepared statements (already built-in)
- Never expose database credentials
- Use environment variables for sensitive data

### 3. Authentication
- Use strong passwords (min 8 characters)
- Enable CSRF protection (built-in)
- Use HTTPS in production
- Set secure session configuration

### 4. Input Validation
- Always validate user input
- Use built-in validation rules
- Sanitize output to prevent XSS

### 5. File Permissions
```bash
chmod -R 755 storage/
chmod -R 755 public/
```

### 6. Dependencies
- Keep PHP updated (8.1+)
- Regularly run `composer update`
- Monitor security advisories

## Known Security Features

SwiftPHP includes these security features by default:

- ✅ CSRF Protection
- ✅ XSS Prevention (auto-escaping in views)
- ✅ SQL Injection Prevention (prepared statements)
- ✅ Argon2ID Password Hashing
- ✅ Secure Session Management
- ✅ Input Validation
- ✅ Environment Variable Protection

## Security Updates

Security updates are released as soon as possible. Subscribe to:
- GitHub Security Advisories
- Release notifications

Thank you for helping keep SwiftPHP secure! 🔒
