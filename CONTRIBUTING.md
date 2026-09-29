# Contributing

Use short descriptive branches such as `feature/rest-items` or `fix/nonce-check` and explain behavior and migration effects in each pull request. Keep controllers thin, use contracts for persistence boundaries, validate at entry points, and escape in templates. Register WordPress hooks through providers or registrar classes.

Before opening a pull request, run `composer quality`, `npm run lint`, `npm test`, and `npm run build`. Run `vendor/bin/phpunit -c phpunit-wp.xml.dist` when a WordPress test suite is available. Schema changes need repeat activation and upgrade tests. New public hooks, routes, options, and tables must be documented in `README.md` and `CHANGELOG.md`.

Use PSR-12 for namespaced PHP. PHPCS additionally enforces WordPress security and API checks; targeted ignores need a reason. Keep commits focused, with a clear imperative summary. Do not commit secrets, vendor packages, Node modules, or generated ZIP files.
