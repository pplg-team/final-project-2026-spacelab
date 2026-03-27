import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                display: ["Space Grotesk", "sans-serif"],
                body: ["Inter", "sans-serif"],
            },
        },
    },

    plugins: [forms],
};
