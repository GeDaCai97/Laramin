import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
    root: 'src',
    build: {
        outDir: '../public/assets',
        manifest: true,
        manifestFileName: 'manifest.json',
        emptyOutDir: true,
        rollupOptions: {
            input: {
                main: path.resolve(__dirname, 'src/js/main.js'),
            }
        }
    },
    server: {
        origin: 'http://localhost:5173',
        cors: true,
        strictPort: true,
    }
})