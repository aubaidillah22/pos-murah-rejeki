import defaultTheme from 'tailwindcss/defaultTheme';

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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: 'rgb(var(--color-emerald-50) / <alpha-value>)',
                    100: 'rgb(var(--color-emerald-100) / <alpha-value>)',
                    200: 'rgb(var(--color-emerald-200) / <alpha-value>)',
                    300: 'rgb(var(--color-emerald-300) / <alpha-value>)',
                    400: 'rgb(var(--color-emerald-400) / <alpha-value>)',
                    500: 'rgb(var(--color-emerald-500) / <alpha-value>)',
                    600: 'rgb(var(--color-emerald-600) / <alpha-value>)',
                    700: 'rgb(var(--color-emerald-700) / <alpha-value>)',
                    800: 'rgb(var(--color-emerald-800) / <alpha-value>)',
                    900: 'rgb(var(--color-emerald-900) / <alpha-value>)',
                },
                emerald: {
                    50: 'rgb(var(--color-emerald-50) / <alpha-value>)',
                    100: 'rgb(var(--color-emerald-100) / <alpha-value>)',
                    200: 'rgb(var(--color-emerald-200) / <alpha-value>)',
                    300: 'rgb(var(--color-emerald-300) / <alpha-value>)',
                    400: 'rgb(var(--color-emerald-400) / <alpha-value>)',
                    500: 'rgb(var(--color-emerald-500) / <alpha-value>)',
                    600: 'rgb(var(--color-emerald-600) / <alpha-value>)',
                    700: 'rgb(var(--color-emerald-700) / <alpha-value>)',
                    800: 'rgb(var(--color-emerald-800) / <alpha-value>)',
                    900: 'rgb(var(--color-emerald-900) / <alpha-value>)',
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
