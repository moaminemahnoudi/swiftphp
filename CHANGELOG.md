# Changelog

All notable changes to SwiftPHP will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2025-12-03

### Added
- 🧪 Automated Testing Suite - Added PHPUnit integration with Unit and Feature tests
- 🔍 Static Analysis - Added PHPStan level 5 integration for code quality
- 👷 CI/CD - Added GitHub Actions workflow for automated testing
- 📚 Documentation - Added Architecture Overview and Getting Started guides
- 🛡️ Security - Enhanced CSRF protection and input sanitization tests
- 📝 Example - Added Task CRUD example with controller, model, and migration
- 🎯 Code Quality - Added `declare(strict_types=1)` to all core files
- 🏗️ Architecture - Created interface contracts for Repository and Cache
- 📋 Standards - Added code quality documentation and pre-commit checklist

### Fixed
- 🐛 Router - Fixed dependency injection in Router constructor
- 🔧 Error Handler - Improved testability by respecting APP_ENV
- 🎨 Error Page - Redesigned with modern dark theme inspired by Laravel Ignition

## [1.0.0] - 2024-12-02

### Added
- 🤖 GenAI CLI - AI-powered code generation from natural language descriptions
- 🔐 Built-in Authentication - Complete login/register/logout system with session management
- 👥 Multi-Role Authorization - Role-based access control (RBAC) with permissions
- 🏢 Multi-Tenant Support - SaaS-ready with automatic tenant isolation
- 📊 Export System - Export to Excel, PDF, CSV, JSON, XML in one line
- 🛡️ AI-Powered Error Handling - Beautiful error pages with intelligent debugging hints
- 🌐 Request & Response Objects - Clean HTTP handling
- ✅ Advanced Validation - 15+ built-in validation rules
- 📦 Laravel-like Collections - Fluent data manipulation
- 🔧 Helper Functions - view(), json(), redirect(), collect(), env(), dd(), dump()
- 💉 Dependency Injection with PHP 8.1+ Attributes
- 🛠️ Middleware System - AuthMiddleware, CorsMiddleware, RateLimitMiddleware
- 🗄️ Model Relations - hasMany, belongsTo, hasOne with eager loading
- 🚀 Fluent Routing API - Route groups, prefixes, middleware chaining
- 🌍 Environment Configuration - .env file support
- 🎨 Blade-like Template Engine - Modern templating with inheritance

### Changed
- Complete framework rewrite focused on "Less Code, Big Results"
- Reduced code volume by 50-70% compared to v1.x
- Updated documentation with comprehensive guides
- Improved performance and memory efficiency

### Documentation
- Added comprehensive framework documentation (docs.html)
- Added GenAI CLI Guide
- Added Authentication Quick Setup Guide
- Added Auth Features Guide
- Added Export System Guide
- Added Error Handling Guide
- Added Quick Reference Cheatsheet
- Added Improvements Summary

---

[1.0.0]: https://github.com/swiftphp/installer/releases/tag/v1.0.0
