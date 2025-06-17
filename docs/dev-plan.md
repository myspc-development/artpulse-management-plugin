# 🛠 ArtPulse Management Plugin — Development Plan

## 📌 Purpose

This document outlines planned enhancements for the ArtPulse Management Plugin. It breaks down features into logical phases, prioritizes tasks, and tracks development progress.

---

## 🚧 Roadmap & Phases

| Phase   | Feature Stream         | Deliverables                                                                 | Priority | Owner / Notes              |
|---------|------------------------|------------------------------------------------------------------------------|----------|-----------------------------|
| **A**   | ✅ Testing              | - Unit tests for `processExpirations()`<br>- Tests for `handleStripeWebhook()` | High     | CI integrated via PHPUnit   |
| **B**   | ✅ CI / CD              | - GitHub Actions workflow<br>- Code coverage reports (HTML, XML)<br>- Lint on push | High     | `phpunit.xml.dist` ready    |
| **C**   | ⚙️ Performance         | - Transient caching for REST CPT endpoints<br>- Minified JS/CSS bundles     | Medium   | Consider object caching     |
| **D**   | 🎨 UX Polish           | - Dashboard widget styling<br>- Empty-state UI for user dashboard views     | Medium   | Use WordPress dashicons     |
| **E**   | 💳 Stripe Extensions    | - Stripe webhook validation<br>- Refund and cancellation endpoints          | Medium   | Manual webhook config needed |
| **F**   | 🌐 Multisite Support    | - Validate CPT visibility per site<br>- Network-wide roles                  | Low      | Add constants guard checks  |
| **G**   | 🌍 Internationalization | - Full i18n via `__()` and `_e()`<br>- POT file generation via WP CLI       | Low      | Text domain already set     |

---

## 📁 Technical Debt Cleanup

- ❌ Remove hardcoded capabilities in `PostTypeRegistrar`
- 🔁 Refactor duplicated `register_post_meta` calls into a utility
- 🧪 Introduce test coverage for role/cap management in `AccessControlManager`
- 📦 Split `Plugin.php` into Bootloader + Service Registration

---

## ✅ Completed (Archive)

- ✅ Composer autoloader integration
- ✅ Custom Post Types + REST fields
- ✅ Membership tier logic + stripe connection
- ✅ Initial PHPUnit setup
- ✅ Basic CI pipeline on `push`/`pull_request`

---

## 📅 Suggested Timeline

| Month     | Milestones                                    |
|-----------|-----------------------------------------------|
| June '25  | Core test coverage + Stripe webhook validation |
| July '25  | REST caching + Admin UX                        |
| August '25| Translations + optional multisite support      |

---

## 🔗 References

- WP REST API Handbook: https://developer.wordpress.org/rest-api/
- Stripe PHP SDK: https://github.com/stripe/stripe-php
- WP CLI POT tools: https://developer.wordpress.org/cli/commands/i18n/

---

## 📄 License

GPL-2.0 © Craig / ArtPulse
