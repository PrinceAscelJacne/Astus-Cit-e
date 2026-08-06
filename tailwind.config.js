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
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                display: ['Poppins', ...defaultTheme.fontFamily.sans],
            },

            /*
             * Palette Astuscité. Les valeurs viennent de la charte du site
             * vitrine (main.css) : rien n'est inventé ici.
             *   accent      #1977cc  couleur de marque, pour les aplats
             *   accent.fort #1668b3  même teinte, plus dense, pour le texte
             *                        sur fond clair (5,73 contre 4,61)
             *   ink         #1b3149  déclinaison sombre du marine #2c4964,
             *                        pour la barre latérale
             */
            colors: {
                ink: {
                    DEFAULT: '#1b3149',
                    2: '#2c4964',
                    soft: '#44546a',
                    muted: '#5f6d7a',
                },
                accent: {
                    DEFAULT: '#1977cc',
                    fort: '#1668b3',
                    sombre: '#12548c',
                },
                paper: {
                    DEFAULT: '#ffffff',
                    alt: '#f4f7fb',
                },
                line: '#e2e8f0',
            },

            transitionTimingFunction: {
                swift: 'cubic-bezier(0.4, 0, 0.2, 1)',
            },
        },
    },

    plugins: [forms, typography],
};
