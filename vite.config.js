import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],

    server: {
        proxy: {
            '/api': {
                target: 'http://monitoring.tivokasiub.cloud',
                changeOrigin: true,
                secure: false,
            },
        },
        cors: true,
    }    
});


