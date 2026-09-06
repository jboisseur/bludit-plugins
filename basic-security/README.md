# Basic security
Basic security overview and settings: configure IP blocking policy and edit path to access admin panel.

Source of security recommendations: (Bludit documentation)[https://docs.bludit.com/en/security].

## Key Features
*   Tracks failed login attempts, blocks suspicious IPs for a customizable duration
*   Provides security recommendations including the ability to update path to admin panel

**Suspicious IPs overview and settings**
Get and set information hold within Security Object, `$security`.

**Update path to admin panel**
Get and set the `ADMIN_URI_FILTER` variable hold within `variables.php`. 
- Input is restricted to alphanumeric characters, hyphens, and underscores (`^[a-zA-Z0-9_-]+$`) to prevent syntax errors or broken URLs.
- The plugin uses a session queue, preventing white screens (500 errors) or instant disconnects.

## Requirements
*   **Bludit CMS v3.x or later**
*   **File Permissions**: The file `/bl-kernel/boot/variables.php` must be writable by PHP.
    *   *Note for Lolipop Hosting Users*: Since Lolipop uses `suEXEC` (PHP runs under your FTP user account), the default permission **`644`** is perfectly sufficient and recommended for best security!

## Credits
*   **Original Author**: jboisseur (<https://jboisseur.xyz>)
*   **Update path to admin panel**: developed by HATTA (<https://hattantoco.com> / GitHub: <https://github.com/HATTANTOCO>), adding the session-delayed core rewrite mechanism inspired by the lifecycle pattern of `PluginAutosaveCleaner`.