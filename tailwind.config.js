import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            // Mirrors the tokens in public/frontend/assets/css/style.css so the
            // dashboard and the public site read as one product.
            colors: {
                pitch: {
                    950: '#040907',
                    900: '#07110c',
                    850: '#0a1710',
                    800: '#0e2117',
                    700: '#15301f',
                },
                brand: {
                    DEFAULT: '#009a56',
                    light: '#00b86b',
                    dark: '#005f38',
                },
                gold: {
                    DEFAULT: '#d9ad45',
                    light: '#f2cf70',
                    dark: '#bd8d2d',
                },
                ink: {
                    DEFAULT: '#eef5f0',
                    2: '#b8c4bd',
                    muted: '#9aa8a0',
                    faint: '#7d8b83',
                },
                danger: '#ff6b5f',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['"Barlow Condensed"', ...defaultTheme.fontFamily.sans],
            },
            borderColor: {
                line: 'rgba(217, 173, 69, .16)',
                'line-strong': 'rgba(217, 173, 69, .42)',
                subtle: 'rgba(255, 255, 255, .08)',
            },
            boxShadow: {
                card: '0 8px 24px rgba(0, 0, 0, .18)',
                pop: '0 24px 60px rgba(0, 0, 0, .34)',
            },
            transitionTimingFunction: {
                smooth: 'cubic-bezier(.2, .7, .2, 1)',
            },
            minHeight: {
                touch: '44px',
            },
        },
    },

    plugins: [forms],
};
