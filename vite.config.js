import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  server: {
    host: '0.0.0.0', // Critical: allows LAN access
    port: 8080,
    strictPort: true,
    open: false, // Prevent auto-browser launch
  },
});