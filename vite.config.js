import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    host: '0.0.0.0', // Listen on all network interfaces
    port: 8080,      // Default port
    strictPort: true,
    open: false,     // Don't auto-open browser
    
    // Allow connections from other devices on the network
    hmr: {
      protocol: 'ws', // Use WebSocket for HMR
      host: '0.0.0.0', // WebSocket host
      port: 8080,
    },
    
    // Proxy configuration (optional - for API calls)
    // proxy: {
    //   '/api': {
    //     target: 'http://localhost:8081', // Your PHP backend
    //     changeOrigin: true,
    //     secure: false,
    //   },
    // },
  },
  
  // Build configuration
  build: {
    outDir: 'dist',
    assetsDir: 'assets',
    sourcemap: true,
  },
  
  // CSS configuration
  css: {
    preprocessorOptions: {
      scss: {
        api: 'modern',
      },
    },
  },
  
  // Optimize dependencies
  optimizeDeps: {
    include: ['react', 'react-dom'],
  },
})