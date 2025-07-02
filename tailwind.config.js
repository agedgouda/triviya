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
                triviya: {
                    lightBackground: '#E6E7F6',
                    red: '#FB38B0',
                    lightRed: '#FFB2E1',
                    darkRed: '#D30082',
                    redHover: '#EC6ABA',
                    redPress: '#C42487',
                    lightPurple: '#A93390',
                    purple: '#651A72',
                    darkPurple: '#4B145B',
                    grayText: '#9D9D9D',
                    darkText: '#141414',
                    lightText: '#FFFFFF',
                    lightGray: '#D7D7D7',
                    darkGray: '#363636',
                    black: '#141414',
                  },
              },
        },
    },

    plugins: [forms, typography],
};
