# ArtPulse Management Plugin

ArtPulse Management provides the main infrastructure for the ArtPulse platform. It registers custom post types, manages memberships and payments via Stripe, exposes REST endpoints, and includes Gutenberg blocks for directory and account pages. The plugin targets WordPress 6.8 and PHP 8.3.

## Installation

1. Clone this repository into your `wp-content/plugins` directory.
2. Install PHP dependencies with `composer install`.
3. Install JavaScript dependencies with `npm install`.
4. Build the front‑end assets using `npm run build`.
5. Activate **ArtPulse Management** from the WordPress admin panel.

## Development

- `npm start` — rebuilds blocks in watch mode.
- `composer test` — runs the PHPUnit test suite.
- `composer cs` — checks coding standards.

For detailed documentation and development notes, see the [docs/](docs/) directory.
