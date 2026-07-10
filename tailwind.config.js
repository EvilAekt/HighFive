import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#fafafa',
                    100: '#f5f5f5',
                    200: '#e8e8e8',
                    300: '#d0d0d0',
                    400: '#a0a0a0',
                    500: '#707070',
                    600: '#505050',
                    700: '#333333',
                    800: '#1a1a1a',
                    900: '#0d0d0d',
                    950: '#000000',
                },
                accent: {
                    DEFAULT: '#000000',
                    light: '#333333',
                    dark: '#000000',
                },
            },
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
                serif: ['Playfair Display', 'Georgia', 'serif'],
            },
        },
    },
    plugins: [
        forms,
    ],
};
