import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [laravel(['resources/css/app.css', 'resources/js/app.js'])],
    resolve: {
        alias: {
            jquery: 'jquery/src/jquery', // Lebih aman daripada dist
        },
    },
});
