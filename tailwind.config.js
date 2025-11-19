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
                sans: ['Inter', ...defaultTheme.fontFamily.sans], // Fuente Oficial
            },
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
                    // --- ADN CORPORATIVO (ESTILO STRIPE/SHOPIFY) ---
                    
                    "primary": "#0f172a",          // Slate 900 (Botones principales oscuros y serios)
                    "primary-content": "#ffffff",  // Texto blanco en botones
                    
                    "secondary": "#3b82f6",        // Azul Tech (Para iconos, enlaces y acentos)
                    "secondary-content": "#ffffff",

                    "accent": "#6366f1",           // Índigo (Detalles extra)
                    
                    "neutral": "#f1f5f9",          // Gris muy claro (Fondos de cabeceras)
                    "neutral-content": "#0f172a",  // Texto oscuro

                    "base-100": "#ffffff",         // BLANCO PURO (Tarjetas, Sidebar, Modales)
                    "base-200": "#f8fafc",         // GRIS CÁLIDO (Fondo de pantalla)
                    "base-300": "#e2e8f0",         // BORDES SUTILES (Gris claro)
                    "base-content": "#334155",     // TEXTO PRINCIPAL (Slate 700 - Legible y elegante)

                    "info": "#0ea5e9",
                    "success": "#10b981",
                    "warning": "#f59e0b",
                    "error": "#ef4444",

                    // FORMAS (Más cuadradas y profesionales)
                    "--rounded-box": "0.5rem",     // Bordes de tarjetas
                    "--rounded-btn": "0.35rem",    // Botones más serios (menos redondos)
                    "--rounded-badge": "1rem",     
                    "--animation-btn": "0.2s",     // Click rápido
                    "--btn-focus-scale": "0.98",   // Efecto sutil
                    "--tab-radius": "0.3rem",
                },
            },
        ],
        darkTheme: "repair-pro", // Forzar este tema siempre
    },
};
