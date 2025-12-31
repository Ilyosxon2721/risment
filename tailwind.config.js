import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    DEFAULT: '#CB4FE4',
                    dark: '#8E2BC6',
                },
                bg: {
                    DEFAULT: '#FFFFFF',
                    soft: '#F6F7FB',
                },
                text: {
                    DEFAULT: '#0B0B10',
                    muted: '#5E6278',
                },
                'brand-border': '#E9ECF2',
                success: '#0ABB87',
                warning: '#FFA800',
                error: '#F1416C',
                info: '#009EF7',
            },
            fontFamily: {
                heading: ['Manrope', 'sans-serif'],
                body: ['Inter', 'sans-serif'],
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                'h1': ['40px', { lineHeight: '1.2', fontWeight: '700' }],
                'h2': ['32px', { lineHeight: '1.3', fontWeight: '700' }],
                'h3': ['24px', { lineHeight: '1.4', fontWeight: '600' }],
                'h4': ['20px', { lineHeight: '1.5', fontWeight: '600' }],
                'body-l': ['18px', { lineHeight: '1.6', fontWeight: '400' }],
                'body-m': ['16px', { lineHeight: '1.6', fontWeight: '400' }],
                'body-s': ['14px', { lineHeight: '1.5', fontWeight: '400' }],
                'caption': ['12px', { lineHeight: '1.4', fontWeight: '400' }],
                'price': ['24px', { lineHeight: '1.2', fontWeight: '700' }],
            },
            spacing: {
                '18': '4.5rem',
                '22': '5.5rem',
            },
            maxWidth: {
                'container': '1200px',
            },
            borderRadius: {
                'card': '16px',
                'btn': '12px',
            },
            boxShadow: {
                'card': '0 4px 20px rgba(11, 11, 16, 0.08)',
            },
        },
    },

    plugins: [forms],
};
