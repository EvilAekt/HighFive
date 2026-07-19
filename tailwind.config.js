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
                onyx: {
                    50: '#f4f4f4',
                    100: '#e8e8e8',
                    200: '#c5c5c5',
                    300: '#a3a3a3',
                    400: '#5e5e5e',
                    500: '#1a1a1a',
                    600: '#171717',
                    700: '#141414',
                    800: '#121212',
                    900: '#0f0f0f',
                    950: '#0a0a0a',
                },
            },
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
                serif: ['Playfair Display', 'Georgia', 'serif'],
            },
            keyframes: {
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                }
            },
            animation: {
                'fade-in': 'fade-in 0.5s ease-out',
                'fade-up': 'fade-up 0.5s ease-out forwards',
            }
        },
    },
    plugins: [
        forms,
    ],
};
