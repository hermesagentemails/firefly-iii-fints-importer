import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
  root: '.',
  base: './',
  server: {
    host: '0.0.0.0',
    port: 3000,
    strictPort: false,
    hmr: true,
  },
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    rollupOptions: {
      input: path.resolve(__dirname, 'public/index.html'),
    },
  },
  resolve: {
    extensions: ['.js', '.jsx', '.ts', '.tsx', '.json'],
  },
  optimizeDeps: {
    include: ['bootstrap', 'bootstrap-icons'],
  },
  ssr: {
    noExternal: ['bootstrap'],
  },
});