# WordPress MVC Starter

A WordPress-native plugin application skeleton for PHP 8.2+ and WordPress 6.5+. It borrows Laravel's container, two-phase service providers, actions, form requests, and dot-notation views while leaving HTTP, authentication, database connections, hooks, REST, Cron, and administration with WordPress. No Laravel or Illuminate package is installed.

## Install and release

For development, place this directory in `wp-content/plugins/`, then run:

```bash
composer install
npm ci
npm run build
```

Activate **WordPress MVC Starter** in WordPress. The dashboard appears under **MVC Starter**. Insert the `[starterkit_greeting]` shortcode or the **MVC Starter Greeting** block to show the configured greeting. Production installs should use `dist/wordpress-mvc-starter.zip`, built with `bash scripts/package.sh` after `npm run build`. The ZIP contains production Composer autoload files and built assets, and excludes development tooling.

The plugin checks PHP and WordPress versions before loading Composer. A missing autoloader produces an administrator notice. Network activation is deliberately rejected; activate per site on multisite. Per-site options, tables, and Cron events use the current site's context.

## Request path

```text
plugin-name.php → bootstrap/app.php → Application → register every provider → boot every provider
WordPress REST/admin/AJAX/shortcode/block/Cron/CLI hook → router/controller
  → validated request → action/service → repository or WordPress adapter
  → response/view → WordPress
```

`app/Foundation/Container.php` supports transient and singleton bindings, instances, automatic constructor resolution, method injection, overrides, and cycle errors. Bind interfaces in a provider; resolve controllers through the container. `config/*.php` returns arrays, read through `$app->config()->get('routes.rest_namespace')`. No plugin `.env` file or alternate WordPress bootstrap is used.

Providers are in `bootstrap/providers.php`. Their `register()` methods install bindings before any `boot()` runs. `RoutingServiceProvider` loads `routes/api.php`, `routes/ajax.php`, `routes/admin.php`, and `routes/console.php`; routes are registered at WordPress's relevant hooks. `PluginServiceProvider` registers translation, the shortcode, block, assets, Cron, and CLI. Heavy services are resolved only when needed.

## Complete example

The settings flow is intentionally small but functional. `UpdateSettingsRequest` checks capability and validates an allowlist. `SettingsData` sanitizes canonical values. `UpdateSettings` saves through `SettingsRepository`, invalidates the greeting cache, dispatches `SettingsUpdated`, and fires `starterkit_mvc/settings/updated`. The admin controller returns `ViewResponse`; REST and AJAX controllers return `JsonResponse`. The repository uses one option record with defaults.

| Entry point | Address | Protection |
| --- | --- | --- |
| Admin form | MVC Starter menu → `admin-post.php` | `manage_options` and nonce |
| REST read/write | `/wp-json/wordpress-mvc-starter/v1/settings` | `manage_options`; write also uses `SettingsPolicy` |
| AJAX write | `admin-ajax.php?action=starterkit_mvc_update_settings` | login, `manage_options`, and `_ajax_nonce` for `starterkit_mvc_update_settings` |
| Frontend | shortcode or block | renders escaped view; no settings disclosure endpoint |

REST routes default to denied access. Call `->public()` only for an intentionally public endpoint. AJAX routes register `wp_ajax_nopriv_*` only with explicit `->public()` and still require a nonce. Nonces protect against CSRF, while capabilities and policies authorize actions. WordPress handles REST cookie or application-password authentication.

## Extend the application

**Controller and route.** Add a controller under `app/Http/Controllers/Rest`, inject contracts, and declare a route:

```php
$router->get('/items', [ItemController::class, 'index'])
    ->capability('read');
```

The router resolves the controller and passes a `Request`. It exposes `input()`, `query()`, `route()`, `file()`, `header()`, `method()`, `user()`, and `nonce()` without reaching into superglobals in controllers. Specify `->request(StoreItemRequest::class)` for a specialized request and `->args([...])` for WordPress REST argument schemas. A request can define `authorize()` and `rules()`; `Validator` handles required, nullable, scalar, email, URL, bounds, membership, regex, and date rules. Custom rules implement `app/Rules/Rule.php`. `ValidationException` becomes a safe REST 422 or AJAX 422 response.

**Action, service, DTO, entity, and repository.** Put one use case in an invokable class under `app/Actions`. Put shared domain operations in `app/Services`. Use typed DTOs under `app/Data` at boundaries and immutable entities under `app/Models` when a feature has meaningful domain behavior. Define persistence contracts under `app/Contracts/Repositories` and WordPress implementations under `app/Repositories/WordPress`. For example, a custom table repository can inject `Contracts\Database\Connection`; a post-backed repository can use WordPress post APIs. Never put SQL in controllers or query data from views.

```php
// In a service provider's register() method.
$this->app->singleton(ItemRepository::class, WordPressItemRepository::class);
```

**Custom tables and migrations.** Native posts/options should remain the default when they fit the data. For a custom table, add a numbered PHP file under `database/migrations` that returns an object implementing `Database\Schema\Migration`. Its `up(Schema $schema)` can call:

```php
$schema->create('starterkit_items', static function (Blueprint $table): void {
    $table->id();
    $table->string('name')->index('name');
    $table->datetime('created_at');
});
```

The migrator sorts filenames, records each successful file, and resumes after partial completion. `dbDelta()` creates or updates tables. Schema upgrades occur on activation or when the installed version in the Options API is lower than `config/database.php`'s `schema_version`; they are not rerun when versions match. Increment the schema version with every new migration. `down()` is available for deliberate maintenance; automatic production downgrades never run. Table names are prefixed from `$wpdb->prefix`; SQL values in `Connection::select()` must be bound through placeholders. Put custom table suffixes in `config/database.php`'s `tables` list only if they should be deleted when uninstall deletion is enabled. Keep repository collection queries paginated and capped.

**Views, partials, components, and theme overrides.** `ViewFactory::make('admin.settings', $data)` maps to `resources/views/admin/settings.php`. Templates receive an explicit `$data` context with `get()` and `render()`; `extract()` is never used. `render('components.notice', ['message' => ...])` composes a partial or component. A layout can render a content view and then a layout view through the same factory. Only names beginning with `frontend.` may be overridden at `active-theme/wordpress-mvc-starter/<name>.php`; admin and internal views are never overridden. Keep templates presentation-only and escape at output.

**WordPress hooks and screens.** Register class callbacks through `WordPress\Hooks\HookRegistrar` or a provider. Put meaningful post type and taxonomy registrations in dedicated classes and hook them to `init`, using WordPress's `register_post_type()` and `register_taxonomy()`. Add menus through `AdminRouter::menu()` or `submenu()` and state-changing admin forms through `AdminRouter::post()` with capability and nonce. Submenus can target `tools.php` or `options-general.php` for native tools or settings screens. WordPress list tables belong behind a WordPress adapter; repositories should supply paginated data. For shortcodes, follow `GreetingShortcode`; callbacks return markup. For blocks, follow `GreetingBlock` and its `block.json`, with server rendering and an editor script. This skeleton does not register unneeded post types, taxonomies, or widgets.

**Cron and jobs.** Jobs implement `Contracts\Jobs\Job::handle()`. Register permitted classes with `CronRegistrar::allow()` or `recurring()` during provider registration. `JobDispatcher::dispatchNow()` runs synchronously; `later()` schedules one WordPress Cron event. `CronRegistrar` prevents duplicate schedules and clears its jobs on deactivation. WordPress Cron runs when traffic triggers it; use a real queue adapter for strict delivery or high-volume work.

**CLI, events, and policies.** Add callable command classes under `app/Console/Commands` and map them in `routes/console.php`; they register only when WP-CLI is running. The starter includes `wp wordpress-mvc-starter status` and `wp wordpress-mvc-starter migrate`. `Dispatcher` supports multiple dependency-injected synchronous listeners configured in `config/events.php`. Use a policy class, as `SettingsPolicy` does, for resource-level checks in addition to capabilities. Add REST route policies with `->policy(Policy::class, 'ability')`. The stable external hook in the example receives a plain settings array: `starterkit_mvc/settings/updated`. Treat all other classes and hooks as internal until a project explicitly publishes them.

**Assets and translation.** Vite builds `resources/js` and `resources/css` to `public/build`, and `Vite` reads its manifest. Admin assets load only on the starter menu page; frontend assets load only when the shortcode or block renders. Set `STARTERKIT_MVC_VITE_URL` in `wp-config.php` and use WordPress's `local` environment for an explicit development server. CSS selectors are plugin-scoped. User-visible strings use the `wordpress-mvc-starter` text domain; update `languages/wordpress-mvc-starter.pot` when adding strings.

## Lifecycle and data

Activation initializes the settings option, runs pending migrations, records versions, and schedules the example cache job. Repeat activation is safe. A failed upgrade is retried after five minutes, shows an administrator notice, and can be retried immediately with `wp wordpress-mvc-starter migrate`. Deactivation only removes Cron events. Uninstall preserves data by default. Set `delete_data_on_uninstall` in `config/plugin.php` to `true` before distributing a plugin that should delete its options and explicitly listed tables. Network-wide deletion is intentionally unsupported while network activation is unsupported.

## Security and failure handling

Validate input at entry points, sanitize when normalizing for storage, and escape in the view for its output context. Request objects adapt REST or unslashed form data. Admin posts and AJAX verify nonces before creating request objects. REST permissions, AJAX login/capabilities, and admin capabilities are checked independently of the nonce. SQL identifiers come from trusted, validated names; values use `$wpdb->prepare()` or `$wpdb` insert/update/delete methods. The view finder rejects path traversal. Unexpected exceptions become generic user-facing errors, and the debug logger records only exception class names when `WP_DEBUG` is enabled. There is no telemetry, remote call, upload handling, or automatic data deletion.

## Quality commands

```bash
composer validate --strict
composer test
composer analyse
composer phpcs
composer quality
npm run lint
npm test
npm run build
vendor/bin/phpunit -c phpunit-wp.xml.dist # requires WP_TESTS_DIR and a configured WordPress test DB
```

CI tests PHP 8.2–8.5, JS/CSS lint, tests, assets, and the release ZIP. WordPress integration tests are provided in `tests/Integration`; set `WP_TESTS_DIR` to the WordPress test library before running them. A WordPress site and database are not bundled in this repository. See [CONTRIBUTING.md](CONTRIBUTING.md) for change standards.

CI also installs disposable WordPress 6.5 and current WordPress sites, then runs the `tests/Integration/*-smoke.php` files through WP-CLI. Locally, run `wp eval-file tests/Integration/smoke.php --path=/path/to/wordpress` after activation; the smoke scripts expect an administrator named `starterkit` in a disposable test site.

## Design references

- [Laravel 13 request lifecycle and service providers](https://laravel.com/docs/13.x/lifecycle)
- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress REST route registration](https://developer.wordpress.org/reference/functions/register_rest_route/)
- [WordPress plugin tables and `dbDelta()`](https://developer.wordpress.org/plugins/creating-tables-with-plugins/)
- [WordPress nonces](https://developer.wordpress.org/plugins/security/nonces/)
