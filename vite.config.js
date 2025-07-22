import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';

/**
 * Configuração do Vite
 * 
 * O Vite é um bundler moderno e rápido que compila e serve os assets
 * da aplicação (CSS, JavaScript, etc.) durante o desenvolvimento.
 */

export default defineConfig({
    plugins: [
        // Plugin do Laravel para integração com o framework
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'], // Arquivos de entrada
            refresh: true, // Habilita hot reload
        }),
        // Plugin do Tailwind CSS para processamento de estilos
        tailwindcss(),
        // Plugin do React para suporte a JSX e componentes
        react(),
    ],
});
