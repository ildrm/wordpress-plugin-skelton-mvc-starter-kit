import { defineConfig } from 'vite';

export default defineConfig({
  publicDir: false,
  build: {
    manifest: 'manifest.json',
    outDir: 'public/build',
    emptyOutDir: true,
    rollupOptions: {
      input: ['resources/js/admin.js', 'resources/js/app.js'],
    },
  },
});
