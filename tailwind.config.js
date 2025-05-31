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
    
    safelist: [
        'bg-blue-600',
        'bg-red-500',
        'bg-green-600',
        'bg-gray-300',
        'text-white',
        'text-gray-800',
        'rounded',
        'rounded-md',
        'rounded-lg',
        'hover:bg-blue-700',
        'hover:bg-green-700',
        'hover:bg-gray-400',
        'shadow-lg',
        'focus:ring-2',
        'w-full',
        'py-2',
        'py-3',
        'px-4',
        'px-6',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
