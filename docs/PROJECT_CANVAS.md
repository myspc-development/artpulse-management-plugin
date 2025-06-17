# ArtPulse Management Plugin Canvas

## 1. Purpose
Provide a unified management layer for ArtPulse (WP 6.8, PHP 8.3) including custom post types, membership, payments, directory, analytics, WooCommerce integration, and Gutenberg blocks.

---

## 2. Key Phases & Status

| Phase                                    | Deliverables                                                         | Status      |
|------------------------------------------|----------------------------------------------------------------------|-------------|
| **1. Scaffold & Composer**               | Repo, PSR-4, composer.json, autoload                                 | ✅ Completed |
| **2. Core CPTs & Meta-Boxes**            | PostTypeRegistrar, MetaBoxRegistrar                                   | ✅ Completed |
| **3. Settings & Admin UI**               | SettingsPage, AdminDashboard                                         | ✅ Completed |
| **4. Membership Engine**                 | MembershipManager (Stripe, expirations), REST & cron                 | ✅ Completed |
| **5. Directory & Shortcodes**            | DirectoryManager + `[ap_directory]` + JS/CSS                         | ✅ Completed |
| **6. User Dashboard**                    | UserDashboardManager + `[ap_user_dashboard]` + REST & JS             | ✅ Completed |
| **7. Analytics**                         | AnalyticsManager (GA4 gtag), Dashboard embed, settings fields        | ✅ Completed |
| **8. WooCommerce Integration**           | WooCommerceIntegration, PurchaseShortcode                            | ✅ Completed |
| **9. Front-end “My Account”**            | FrontendMembershipPage, `[ap_membership_account]`, JS/CSS            | ✅ Completed |
| **10. Gutenberg Blocks**                 | Directory & Account blocks, build pipeline                           | ✅ Completed |
| **11. Testing**                          | PHPUnit + BrainMonkey, core tests                                    | 🚧 In Progress  |

---

## 3. Completed Test Coverage

- **MembershipManagerTest**  
  - assignFreeMembership()  
  - welcome email stub  

- **WooCommerceIntegrationTest**  
  - assignMembership() meta & email  

- **PostTypeRegistrarTest**  
  - register_post_type() calls  

---

## 4. Blockers & Risks

- **Bootstrap syntax**: ensure `tests/bootstrap.php` has no stray braces or class definitions  
- **Patchwork conflicts**: only initialize Brain Monkey in tests  
- **Stub coverage**: missing WP functions (`__()`, `current_time()`, `date_i18n()`, etc.)

---

## 5. Next Steps

1. **Fix remaining test bootstrap**  
   - Remove stray code from `bootstrap.php`  
   - Stub any other WP helpers in `tests/bootstrap.php`  

2. **Expand test coverage**  
   - `processExpirations()` in MembershipManager  
   - `handleStripeWebhook()` parsing of sample payloads  

3. **CI Pipeline**  
   - GitHub Actions workflow: `composer install`, `npm ci`, `npm run build`, `phpunit`  

4. **Documentation & Release**  
   - Update README with usage examples and block screenshots  
   - Write CHANGELOG entries for v0.1.0  
   - Tag and publish on WordPress.org  

5. **Polish & UX**  
   - Improve block editor styling  
   - Add loading spinners & error handling for front-end API calls  
