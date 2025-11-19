import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php', // <--- Esto cubre TODAS las carpetas (auth, layouts, livewire)
        './resources/js/**/*.js',
        './vendor/robsontenorio/mary/src/View/Components/**/*.php'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('daisyui'),
    ],

    daisyui: {
        themes: [
            {
                "repair-pro": {
                    // AZUL CORPORATIVO MODERNO (Más vibrante)
                    "primary": "#3b82f6",          // Azul principal
                    "primary-content": "#ffffff",  // Texto sobre azul
                    
                    // SECUNDARIO (Para detalles)
                    "secondary": "#6366f1",        // Índigo
                    "accent": "#06b6d4",           // Cian (para toques eléctricos)
                    
                    // COLORES DE ESTADO (Más suaves, no tan chillones)
                    "info": "#0ea5e9",
                    "success": "#10b981",
                    "warning": "#f59e0b",
                    "error": "#ef4444",

                    // EL SECRETO: LA BASE OSCURA (Deep Slate)
                    "neutral": "#1e293b",          // Gris oscuro para elementos neutros
                    "base-100": "#0f172a",         // Fondo PRINCIPAL (Slate 900)
                    "base-200": "#1e293b",         // Fondo TARJETAS/SIDEBAR (Slate 800)
                    "base-300": "#334155",         // Bordes sutiles (Slate 700)
                    "base-content": "#f1f5f9",     // Texto principal (Casi blanco)

                    // ESTILO DE BORDES (Más moderno)
                    "--rounded-box": "1rem",       // Tarjetas más redondas
                    "--rounded-btn": "0.5rem",     // Botones semiredondos
                    "--rounded-badge": "1.9rem",   // Badges píldora
                    "--animation-btn": "0.25s",    // Click rápido
                    "--animation-input": "0.2s",   // Focus rápido
                    "--btn-focus-scale": "0.95",   // Efecto rebote sutil
                    "--border-btn": "1px",         // Borde fino
                    "--tab-border": "1px",
                    "--tab-radius": "0.5rem",
                },
            },
        ],
    },
};
