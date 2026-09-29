#!/usr/bin/env bash
set -euo pipefail

root_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
stage_dir="$(mktemp -d)"
trap 'rm -rf "$stage_dir"' EXIT
plugin_dir="$stage_dir/wordpress-mvc-starter"
mkdir -p "$plugin_dir" "$root_dir/dist"

if [[ ! -f "$root_dir/public/build/manifest.json" ]]; then
  echo 'Missing public/build/manifest.json. Run npm run build first.' >&2
  exit 1
fi

for item in app bootstrap config database languages public resources routes composer.json composer.lock plugin-name.php uninstall.php LICENSE readme.txt README.md; do
  if [[ -e "$root_dir/$item" ]]; then
    cp -R "$root_dir/$item" "$plugin_dir/$item"
  fi
done

rm -rf "$plugin_dir/resources/js" "$plugin_dir/resources/css"
composer install --working-dir="$plugin_dir" --no-dev --no-interaction --prefer-dist --optimize-autoloader

rm -f "$root_dir/dist/wordpress-mvc-starter.zip"
(
  cd "$stage_dir"
  zip -qr "$root_dir/dist/wordpress-mvc-starter.zip" wordpress-mvc-starter
)

echo "$root_dir/dist/wordpress-mvc-starter.zip"
