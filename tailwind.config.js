import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                betrack: {
                    primary: '#1573F7',     // Azul principal
                    secondary: '#5F3AD7',   // Morado secundario
                    accent: '#029DFF',      // Azul acento
                    dark: '#2F295B',        // Morado oscuro
                    surface: '#FFFFFF',     // Blanco
                    bg: '#F8F9FC',          // Gris claro
                    text: '#202124',        // Texto principal
                },
            },
        },
    },

    plugins: [forms],
};
