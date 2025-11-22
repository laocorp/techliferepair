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
                // Paleta personalizada para "repair-pro"
                primary: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    500: '#3b82f6', // Azul vibrante principal
                    600: '#2563eb', // Azul hover
                    700: '#1d4ed8',
                    900: '#0f172a', // Slate oscuro para textos/fondos
                },
                slate: {
                    50: '#f8fafc', // Fondo principal
                    100: '#f1f5f9', // Fondo secundario
                    200: '#e2e8f0', // Bordes suaves
                    800: '#1e293b', // Texto secundario
                    900: '#0f172a', // Texto principal
                }
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
                "repair-pro": {
                    "primary": "#0f172a",          // Botones principales oscuros (Slate 900)
                    "primary-content": "#ffffff",
                    
                    "secondary": "#3b82f6",        // Azul para acentos e iconos
                    "secondary-content": "#ffffff",

                    "accent": "#6366f1",           // Índigo
                    
                    "neutral": "#f1f5f9",          // Gris muy claro (Fondos secundarios)
                    "neutral-content": "#0f172a",  // Texto oscuro

                    "base-100": "#ffffff",         // Fondo BLANCO PURO (Cards, Sidebar)
                    "base-200": "#f8fafc",         // Fondo GRIS CÁLIDO (Body background)
                    "base-300": "#e2e8f0",         // Bordes sutiles
                    "base-content": "#334155",     // Texto principal (Slate 700)

                    "info": "#0ea5e9",
                    "success": "#10b981",
                    "warning": "#f59e0b",
                    "error": "#ef4444",

                    "--rounded-box": "0.5rem",     // Bordes sutiles
                    "--rounded-btn": "0.35rem",    // Botones semiredondos
                    "--rounded-badge": "1rem",
                    "--animation-btn": "0.2s",
                    "--btn-focus-scale": "0.98",
                    "--tab-radius": "0.3rem",
                },
            },
        ],
        darkTheme: "repair-pro", // Forzamos este tema
    },
};
