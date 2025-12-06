import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // Gradient colors for stat cards
        'from-blue-500', 'to-blue-600',
        'from-green-500', 'to-green-600',
        'from-purple-500', 'to-purple-600',
        'from-orange-500', 'to-orange-600',
        'from-red-500', 'to-red-600',
        'from-indigo-500', 'to-indigo-600',
        'from-pink-500', 'to-pink-600',
        'from-teal-500', 'to-teal-600',
        'from-yellow-500', 'to-yellow-600',
        // Background gradients
        'bg-gradient-to-br',
        'bg-gradient-to-r',
        'bg-gradient-to-l',
        'bg-gradient-to-t',
        'bg-gradient-to-b',
        // Text colors with opacity
        'text-white/95',
        'text-white/90',
        'text-white/80',
        'text-white/70',
        // Background colors with opacity
        'bg-white/20',
        'bg-white/30',
        'bg-white/40',
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
