import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Quicksand', ...defaultTheme.fontFamily.sans],
            },colors: {
                triviyaRegular: '#9022b2',
                triviyaLight: '#7DC9D9',
                triviyaContent: '#9022b2',
                triviyaPink: '#FD67C4',
                triviyaPurple: {
                    900: '#9022b2',
                    800: '#9c35bd',
                    700: '#a94ec7',
                },
              },
        },
    },

    plugins: [forms, typography],
};
