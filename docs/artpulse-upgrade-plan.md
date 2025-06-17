🌟 ArtPulse Plugin Upgrade Plan: Full-Featured Art Community Hub

This document outlines the planned feature upgrades to transform the ArtPulse Management Plugin into a fully functional art community platform.

🧱 Core Feature Upgrades

1. 🎨 Public Forms with Moderation Workflow

Enable front-end forms for:

Form Type

CPT / Entity

Fields

Approval?

Artist Registration

artpulse_artist

Name, bio, website, genre, contact info

✅

Organization Signup

artpulse_org

Name, address, mission, logo

✅

Event Submission

artpulse_event

Title, date, location, description, image

✅

Member Registration

user + tier

Name, email, membership level, password

✅

Instructions:

Create one shortcode file per form type (e.g., SubmitArtistForm.php)

Use WP_REST_Request or AJAX handler for form data processing

Store submissions as draft posts or pending status

Add an admin approval column via manage_posts_columns

2. 📍 Address Autocomplete Integration

Enhance address fields using:

Select2 for country/state/city (GeoNames API)

Google Places API for full-text address autocomplete

Instructions:

Add API settings to SettingsPage.php

Create service: AddressResolver::resolve()

Enqueue Select2 and Google Maps scripts

Use JS to populate city/state fields and hidden lat/lng

3. ⚙️ Admin Configuration Panel

New settings tab under "ArtPulse Settings":

Google/GeoNames API keys

Enable/disable individual forms

Enable moderation

Notification toggles

Instructions:

Add form config section to SettingsPage.php

Store keys using get_option('artpulse_settings')

Use conditional logic in form shortcodes to check if enabled

4. 🧼 Admin Review Panel

Add a backend panel for reviewing and moderating submitted content:

List of all pending submissions (artists, orgs, events)

Inline approval/decline buttons

Filters for status/type/date

Bulk actions

Instructions:

Create a submenu under "ArtPulse" in admin

Use WP_List_Table for rendering

Integrate with post status updates

Optional: Add approval comment/log field

5. 🔢 Custom Meta Fields & Linking

Add custom meta fields for each CPT:

👤 artpulse_artist

Meta Key

Description

Type

_ap_artist_bio

Artist biography

string

_ap_artist_org

Linked organization ID

integer

_ap_artist_artworks

Related artworks (many)

array of int

🏠 artpulse_org

Meta Key

Description

Type

_ap_org_address

Physical address

string

_ap_org_latlng

Latitude/Longitude pair

object

_ap_org_mission

Org purpose/mission

string

🖼 artpulse_artwork

Meta Key

Label

Type

Required?

artwork_title

Artwork Title

string

✅

artwork_artist

Artist Name

string

✅

artwork_medium

Medium

string

✅

artwork_dimensions

Dimensions

string

❌

artwork_year

Year Created

integer

✅

artwork_materials

Materials Used

textarea

❌

artwork_price

Price

string

❌

artwork_provenance

Provenance

textarea

❌

artwork_edition

Edition

string

❌

artwork_tags

Tags

string

❌

artwork_description

Detailed Description

textarea

✅

artwork_video_url

Video URL

string

❌

artwork_gallery_images[]

Gallery Images

array

❌

🎭 artpulse_event

Meta Key

Label

Type

Required?

title

Event Title

string

✅

description

Description

textarea

✅

date

Date

string

✅

country/state/city

Location Region

select2

✅

suburb/street/postcode

Address

string

✅

latitude/longitude

Geolocation

float

❌

organizer

Organizer Name

string

❌

organizer_email

Organizer Email

string

❌

gallery[]

Gallery Images

file[]

❌

Instructions:

Use register_post_meta or add_post_meta() with REST exposure

Map meta fields across forms, REST output, and dashboard views

Use meta_input during CPT creation

Use relational meta (artist ID, org ID) to link entries together

6. 🧹 Frontend Enhancements

AJAX-based form submissions

reCAPTCHA v3 for spam protection

Inline JS validation with field highlighting

Instructions:

Add AJAX nonce and endpoint handler per form type

Use wp_localize_script for field labels and config

Add reCAPTCHA support via JS and REST validation hook

7. 👥 User Account Dashboard

Enhance [ap_user_dashboard] to include:

User's submitted artists/events/orgs

Edit profile view (linked CPT)

Membership status + upgrade button

Geocoded map of user content

Instructions:

Fetch user-linked CPTs by post_author

Use get_user_meta or CPT connection to show profile info

Embed Google Map with markers from stored lat/lng

🧰 Suggested Technical Structure

Component

Location

Form handlers

src/Ajax/FormSubmissionHandler.phpsrc/Rest/FormApiController.php

Address API service

src/Services/AddressResolver.php

Settings config panel

src/Core/SettingsPage.php

Admin review table

src/Admin/SubmissionReviewPage.php

Meta registration utilities

src/Core/MetaRegistrar.php

Templates / Shortcodes

src/Core/ShortcodeManager.php

JS UI assets

assets/js/forms/

Email Templates

templates/emails/*.php

🔮 Optional Features (Phase 2+)

Multi-language support (i18n-ready forms)

Calendar of approved events (frontend block or shortcode)

Approval/decline email notifications for moderators and users

Analytics dashboard per artist/org (CPT metrics, views, submissions)

Map-based event search with proximity filter

User-to-user messaging or collaboration requests

Featured content slider for home page (highlight artists or events)

Meta field-based search/filter interface

✅ Development Checklist



GPL-2.0 © Craig / ArtPulse

