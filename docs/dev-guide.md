# Developer Guide: ArtPulse Management Plugin

This guide is intended for developers working on or contributing to the ArtPulse Management Plugin. It outlines the plugin architecture, conventions, and workflows for development, testing, and deployment.

---

## 📁 Project Structure

```plaintext
artpulse-management-plugin/
│
├── artpulse-management.php          # Plugin bootstrap
├── composer.json                    # Autoload + dependency config
├── vendor/                          # Composer dependencies (gitignored)
├── src/
│   ├── Core/                        # Core plugin engine + modules
│   ├── Community/                   # User-centric features (favorites, follows)
│   ├── Admin/                       # Admin columns, UI helpers
│   ├── Ajax/                        # Frontend AJAX handlers
│   ├── Rest/                        # REST API endpoints
│   ├── Blocks/                      # Gutenberg blocks
│   ├── Taxonomies/                 # CPT taxonomies
│   └── Templates/                  # Reusable template parts
│
├── tests/                           # PHPUnit tests
├── assets/                          # JS/CSS assets
└── docs/                            # Dev and user documentation
