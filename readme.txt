=== WordPress MVC Starter ===
Contributors: shahinilderemi
Tags: developer, mvc, starter, framework
Requires at least: 6.5
Requires PHP: 8.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A WordPress-native MVC starter kit with dependency injection and Laravel-inspired conventions.

== Description ==

WordPress MVC Starter provides an application container, service providers, plugin lifecycle services, WordPress entry point routers, views, migrations, assets, and an accessible settings example. It does not include Laravel or replace WordPress request handling.

== Installation ==

Install the release ZIP through Plugins > Add New > Upload Plugin, then activate it. Developers cloning the source should run composer install, npm install, and npm run build before activation.

== Frequently Asked Questions ==

= Does this add custom tables by default? =

No. Migrations are available for plugins that need custom tables.

= Is WooCommerce required? =

No. Add integrations in a dedicated provider when needed.

== Changelog ==

= 1.0.0 =

Initial release.
