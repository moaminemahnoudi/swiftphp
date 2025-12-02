# SwiftPHP Framework - Publishing Guide

## 📦 Package Structure

The framework is now ready for Composer/Packagist publishing with:

- ✅ `composer.json` - Complete package metadata
- ✅ `LICENSE` - MIT License
- ✅ `README.md` - Comprehensive documentation
- ✅ `CHANGELOG.md` - Version history
- ✅ `CONTRIBUTING.md` - Contribution guidelines
- ✅ `SECURITY.md` - Security policy
- ✅ `.gitattributes` - Git export configuration

## 🚀 How to Publish to Packagist

### 1. Create GitHub Repository

```bash
# Initialize git (if not already done)
git init
git add .
git commit -m "Initial release v1.0.0"

# Add remote repository
git remote add origin https://github.com/moaminemahnoudi/swiftphp.git
git branch -M main
git push -u origin main
```

### 2. Create a Git Tag

```bash
# Create and push version tag
git tag -a v1.0.0 -m "SwiftPHP v1.0.0 - Modern PHP Framework"
git push origin v1.0.0
```

### 3. Register on Packagist

1. Go to https://packagist.org
2. Sign in with GitHub account
3. Click "Submit"
4. Enter repository URL: `https://github.com/moaminemahnoudi/swiftphp`
5. Click "Check" to validate
6. Click "Submit" to publish

### 4. Set Up Auto-Update Hook (Optional)

Packagist will automatically update when you push new tags if you:
1. Go to your GitHub repository Settings → Webhooks
2. Add webhook URL from Packagist
3. Select "Just the push event"

## 📋 Pre-Publishing Checklist

- [x] composer.json has correct package name
- [x] composer.json has complete metadata (description, keywords, license, authors)
- [x] LICENSE file exists
- [x] README.md is comprehensive
- [x] CHANGELOG.md documents all versions
- [x] CONTRIBUTING.md explains contribution process
- [x] SECURITY.md has security policy
- [x] All code follows PSR-12
- [x] Documentation is complete and accurate
- [x] No hardcoded credentials or sensitive data
- [ ] GitHub repository is public (required for Packagist)
- [ ] Repository has proper .gitignore

## 🔧 Update GitHub Repository Settings

Before publishing, update your GitHub repository:

### Repository Details
- **Description**: "Modern PHP framework with AI-powered CLI, built-in auth, and 70% less code"
- **Website**: Link to your documentation site
- **Topics**: php, framework, mvc, router, orm, validation, authentication, multi-tenant, ai, genai

### Repository Settings
- Make repository public (required for Packagist)
- Enable Issues
- Enable Discussions (optional)
- Add README to repository home

## 📝 Version Tagging

When releasing new versions:

```bash
# Update CHANGELOG.md first
git add CHANGELOG.md
git commit -m "Update changelog for v1.0.1"

# Create and push tag
git tag -a v1.0.1 -m "Bug fixes and improvements"
git push origin v1.0.1
```

Packagist will automatically detect the new tag and update the package.

## 🎯 Installation Command After Publishing

Once published, users can install with:

```bash
composer global require swiftphp.ma/installer
```

Or create new project with:

```bash
composer create-project swiftphp.ma/installer my-app
```

## 🌐 Additional Distribution

Consider also:
- Submit to PHP Framework Interoperability Group (PHP-FIG)
- List on AlternativeTo.net
- Share on PHP community forums
- Write announcement blog posts
- Submit to weekly PHP newsletters

## 📊 Monitoring

After publishing:
- Monitor Packagist download statistics
- Watch GitHub stars and forks
- Respond to issues and pull requests
- Keep documentation updated
- Release regular updates

## 🔗 Useful Links

- Packagist: https://packagist.org
- Composer Documentation: https://getcomposer.org/doc/
- Semantic Versioning: https://semver.org/
- Keep a Changelog: https://keepachangelog.com/

---

**Ready to publish SwiftPHP to the world! 🚀**
