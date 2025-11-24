import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './vendor/mary-ui/src/View/**/*.php',
        './app/View/Components/**/*.php'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    900: '#0c4a6e',
                },
                dark: {
                    800: '#1e293b',
                    900: '#0f172a',
                }
            },
            boxShadow: {
                'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                'glow': '0 0 15px rgba(14, 165, 233, 0.3)',
            }
        },
    },

    plugins: [
        forms,
        require('daisyui'),
    ],

    daisyui: {
        themes: [
            {
                "techlife-v5": {
                    "primary": "#0f172a",          // Slate 900
                    "primary-content": "#ffffff",
                    
                    "secondary": "#3b82f6",        // Azul eléctrico
                    "secondary-content": "#ffffff",

                    "accent": "#0ea5e9",           // Cian
                    
                    "neutral": "#f8fafc",          // Gris muy claro
                    "neutral-content": "#0f172a",

                    "base-100": "#ffffff",         // Blanco Puro
                    "base-200": "#f1f5f9",         // Gris Slate muy suave
                    "base-300": "#e2e8f0",         // Bordes finos
                    "base-content": "#334155",     // Texto Slate 700

                    "info": "#0ea5e9",
                    "success": "#10b981",
                    "warning": "#f59e0b",
                    "error": "#ef4444",

                    "--rounded-box": "1rem",
                    "--rounded-btn": "0.5rem",
                    "--rounded-badge": "9999px",
                    
                    "--animation-btn": "0.2s",
                    "--btn-focus-scale": "0.98",
                    "--tab-radius": "0.5rem",
                },
            },
        ],
        darkTheme: "techlife-v5",
    },
};
