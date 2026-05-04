import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                // Menjadikan Plus Jakarta Sans sebagai prioritas utama, lalu fallback ke Figtree
                sans: ['Plus Jakarta Sans', 'Figtree', ...defaultTheme.fontFamily.sans],
                game: ['Bangers', 'cursive'],
            },
            colors: {
                // Mendaftarkan warna custom dari halaman auth sebelumnya
                excel: {
                    light: '#10b981',
                    dark: '#059669',
                    deep: '#035a41'
                }
            }
        },
    },

    plugins: [forms],
};