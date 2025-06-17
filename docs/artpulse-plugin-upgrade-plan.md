
# 🌟 ArtPulse Plugin Upgrade Plan: Full-Featured Art Community Hub

This document outlines the planned feature upgrades to transform the ArtPulse Management Plugin into a fully functional art community platform.

---

## 🧱 Core Feature Upgrades

### 1. 🎨 Public Forms with Moderation Workflow

Enable front-end forms for:

| Form Type              | CPT / Entity     | Fields                                       | Approval? |
|------------------------|------------------|----------------------------------------------|-----------|
| Artist Registration    | `artpulse_artist`| Name, bio, website, genre, contact info       | ✅        |
| Organization Signup    | `artpulse_org`   | Name, address, mission, logo                  | ✅        |
| Event Submission       | `artpulse_event` | Title, date, location, description, image     | ✅        |
| Member Registration    | `user` + tier    | Name, email, membership level, password       | ✅        |

🔧 **Settings Panel:**
- Define required fields
- Enable moderation workflow
- Enable/disable form types

✅ Use `WP_User` for membership and map to `artpulse_org` or `artpulse_artist` post if applicable.

---

### 2. 📍 Address Autocomplete Integration

Enhance address fields using:

- **Select2** for country/state/city (via GeoNames)
- **Google Places API** for full-text address autocomplete

📦 Auto-fill: city, state, country, lat/lng

🔧 **Admin Settings:**
- `Geonames API username`
- `Google Maps API key`
- Toggle autocomplete/manual

💡 Service: `ArtPulse\Services\AddressResolver::resolve($input)`

---

### 3. ⚙️ Admin Configuration Panel

New settings tab under "ArtPulse Settings":

- API keys (Google, GeoNames)
- Enable/disable forms (checkboxes)
- Moderation toggles
- Email notification options

---

### 4. 🧩 Frontend Enhancements

- AJAX-based form submissions
- reCAPTCHA v3 support
- Inline validation + feedback
- Shortcodes:
  - `[ap_submit_artist_form]`
  - `[ap_register_org_form]`
  - `[ap_submit_event_form]`

---

### 5. 👥 User Account Dashboard

Enhance `[ap_user_dashboard]` to include:

- “My Submissions”
- Linked profile editing (artist/org)
- Membership upgrade options
- Saved locations + geocoded map

---

## 🧰 Suggested Technical Structure

| Component                    | Location                           |
|-----------------------------|------------------------------------|
| Form handlers               | `src/Ajax/FormSubmissionHandler.php`<br>`src/Rest/FormApiController.php` |
| Address API service         | `src/Services/AddressResolver.php` |
| Settings config panel       | `src/Core/SettingsPage.php`        |
| Templates / Shortcodes      | `src/Core/ShortcodeManager.php`    |
| JS UI assets                | `assets/js/forms/`                 |
| Email Templates             | `templates/emails/*.php`           |

---

## 🔮 Optional Features (Phase 2+)

- Multi-language form support
- Calendar view of approved events
- Notification system for approvals
- Analytics for orgs/artists
- Proximity search via lat/lng

---

## ✅ Development Checklist

- [ ] Form shortcodes + templates
- [ ] REST endpoints
- [ ] AddressResolver class
- [ ] Admin settings UI
- [ ] Moderation workflow
- [ ] AJAX + Select2 + Google Maps scripts
- [ ] Dashboard customization

---

GPL-2.0 © Craig / ArtPulse
