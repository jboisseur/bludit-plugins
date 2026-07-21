# Basic security (with Core URL Rewrite Extension)

This is an enhanced fork of the **Basic security** plugin for Bludit. In addition to original IP blocking and login security, this extended version adds a feature to dynamically customize the `ADMIN_URI_FILTER` constant inside `/bl-kernel/boot/variables.php` directly from the admin panel, with a robust **automatic recovery system** after Bludit core updates.

## Key Features (Enhanced Version)

*   **Original Security Features**: Tracks failed login attempts, blocks suspicious IPs for a customizable duration, and provides security recommendations.
*   **Safe Delay-Rewrite Engine**: Modifies the core `variables.php` file securely on the next page reload using a session queue, preventing white screens (500 errors) or instant disconnects.
*   **Zero-Stress Auto Recovery**: When Bludit core files are updated, the `ADMIN_URI_FILTER` automatically resets to default (`admin`). This plugin detects the core reset after your first login and **automatically restores your previous custom URL in the background**. No need to re-click the save button!
*   **Strict Input Validation**: Input is restricted to alphanumeric characters, hyphens, and underscores (`^[a-zA-Z0-9_-]+$`) to prevent syntax errors or broken URLs.

## Requirements

*   **Bludit CMS v3.x or later**
*   **File Permissions**: The file `/bl-kernel/boot/variables.php` must be writable by PHP.
    *   *Note for Lolipop Hosting Users*: Since Lolipop uses `suEXEC` (PHP runs under your FTP user account), the default permission **`644`** is perfectly sufficient and recommended for best security!

## Installation & Setup

1.  **Backup**: Ensure you back up `/bl-kernel/boot/variables.php` before enabling the rewrite features.
2.  **Upload**: Upload or merge the extended code into your `/bl-content/plugins/basic-security/` folder.
3.  **Language Update**: Add the new translation keys into your `languages/ja.json` and `languages/en.json` files.
4.  **Activate**: Go to Bludit Admin Area -> **Settings** -> **Plugins** and activate **Basic security**.
5.  **Configure**: Enter your preferred custom admin URL in the input field and click **Save**. The value will safely update on your next dashboard load.

## Translation Keys (JSON Example)

Add these to your `languages/ja.json`:

```json
{
    "admin-url-filter": "管理画面のURLフィルター設定",
    "core-file-rewrite": "コア書き換え",
    "file-not-writable-warning": "警告: コアファイルに書き込み権限がありません。パーミッションを666等に変更してください:",
    "core-rewrite-info-1": "変更したいURLフィルター値を入力して保存すると、管理URLをカスタマイズします。",
    "core-rewrite-info-2": "保存されたURL値は、次回、管理画面が表示された時点で更新されます。",
    "admin-uri-filter-constant": "ADMIN_URI_FILTER の値",
    "core-rewrite-success-alert": "管理画面URLを「%s」にカスタマイズしました。次回アクセス時からは新しいURLを使用してください。",
    "core-reset-auto-recovered-alert": "コアファイルの更新を検知したため、リセットされたURLフィルターを以前の設定「%s」に全自動で再適用しました。次回アクセスからはこのURLを使用してください。"
}
```

## Credits & License

*   **Original Author**: jboisseur (<https://jboisseur.xyz>)
*   **Extended Recovery Logic**: Developed and enhanced by HATTA (<https://hattantoco.com> / GitHub: <https://github.com/HATTANTOCO>), adding the session-delayed core rewrite mechanism inspired by the lifecycle pattern of `PluginAutosaveCleaner`.
*   **License**: MIT license
