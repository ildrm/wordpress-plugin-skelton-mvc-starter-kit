import js from '@eslint/js';
import globals from 'globals';

export default [
  js.configs.recommended,
  {
    files: ['resources/**/*.js', 'vite.config.js'],
    languageOptions: {
      globals: { ...globals.browser, ...globals.node, wp: 'readonly' },
    },
  },
];
