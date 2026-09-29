import assert from 'node:assert/strict';
import test from 'node:test';
import config from '../vite.config.js';

test('build emits a manifest for the WordPress asset loader', () => {
  assert.equal(config.build.manifest, 'manifest.json');
  assert.deepEqual(config.build.rollupOptions.input, ['resources/js/admin.js', 'resources/js/app.js']);
});
