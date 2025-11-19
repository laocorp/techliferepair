<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación Técnica - TechLife</title>
    <style>
        /* Estilos estilo GitHub README */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #24292e;
            background-color: #fff;
            margin: 0;
            padding: 2rem;
        }
        .container {
            max-width: 980px;
            margin: 0 auto;
            border: 1px solid #e1e4e8;
            padding: 45px;
            border-radius: 6px;
            background-color: #fff;
        }
        h1, h2, h3 { margin-top: 24px; margin-bottom: 16px; font-weight: 600; line-height: 1.25; }
        h1 { font-size: 2em; border-bottom: 1px solid #eaecef; padding-bottom: 0.3em; }
        h2 { font-size: 1.5em; border-bottom: 1px solid #eaecef; padding-bottom: 0.3em; }
        h3 { font-size: 1.25em; }
        p { margin-top: 0; margin-bottom: 16px; }
        code {
            padding: 0.2em 0.4em;
            margin: 0;
            font-size: 85%;
            background-color: #f6f8fa;
            border-radius: 3px;
            font-family: SFMono-Regular, Consolas, "Liberation Mono", Menlo, monospace;
        }
        pre {
            padding: 16px;
            overflow: auto;
            font-size: 85%;
            line-height: 1.45;
            background-color: #1f2937; /* Fondo oscuro para código */
            color: #f8f8f2;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        pre code {
            background-color: transparent;
            padding: 0;
            color: inherit;
        }
        ul { padding-left: 2em; margin-bottom: 16px; }
        li { margin-bottom: 0.25em; }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 16px;
        }
        table th, table td {
            padding: 6px 13px;
            border: 1px solid #dfe2e5;
        }
        table th {
            font-weight: 600;
            background-color: #f6f8fa;
        }
        table tr:nth-child(2n) { background-color: #f6f8fa; }
        .badge { display: inline-block; margin-right: 5px; }
        .diagram {
            background-color: #f6f8fa;
            padding: 20px;
            border-radius: 6px;
            border: 1px solid #e1e4e8;
            font-family: monospace;
            white-space: pre;
            overflow-x: auto;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
        }
    </style>
</head>
<body>

<div class="container">

    <h1>🔧 TechLife - Enterprise Repair Management System</h1>

    <p>
        <img src="https://img.shields.io/badge/version-1.0.0-blue.svg?style=for-the-badge" class="badge">
        <img src="https://img.shields.io/badge/stack-TALL-38bdf8.svg?style=for-the-badge" class="badge">
        <img src="https://img.shields.io/badge/status-Production-success.svg?style=for-the-badge" class="badge">
    </p>

    <p><strong>TechLife</strong> es una plataforma SaaS (Software as a Service) de nivel empresarial diseñada para la gestión integral de Centros de Servicio Autorizados (CAS), talleres de maquinaria industrial y laboratorios técnicos.</p>
    <p>El sistema centraliza la operación técnica, administrativa y financiera en una sola interfaz moderna, oscura y optimizada para la eficiencia.</p>

    <hr>

    <h2>🏗️ Arquitectura del Sistema</h2>
    <div class="diagram">
User [Cliente / Técnico] 
  │
  ▼
HTTPS / Cloudflare
  │
  ▼
Nginx [Web Server] ──▶ PHP 8.3 FPM ──▶ Laravel 12 (Backend)
                                            │
                                            ├──▶ MariaDB (Base de Datos)
                                            ├──▶ DomPDF (Motor de Reportes)
                                            ├──▶ Livewire + Volt (Frontend Reactivo)
                                            └──▶ File Storage (Fotos/Docs)
    </div>

    <hr>

    <h2>🚀 Características Principales</h2>

    <h3>🛠️ Gestión Operativa (Core)</h3>
    <ul>
        <li><strong>CRM de Clientes:</strong> Base de datos indexada con búsqueda instantánea y perfil histórico.</li>
        <li><strong>Gestión de Activos:</strong> Registro de maquinaria por <strong>Número de Serie</strong> único, marca y modelo.</li>
        <li><strong>Órdenes de Trabajo (OT):</strong> Flujo de estados estricto (<em>Recibido -> Diagnóstico -> Espera Repuestos -> Listo -> Entregado</em>).</li>
        <li><strong>Caja de Herramientas:</strong> Cálculo automático de costos (Mano de obra + Repuestos).</li>
    </ul>

    <h3>📦 Inventario y Logística</h3>
    <ul>
        <li><strong>Control de Stock:</strong> Gestión de repuestos con SKU y ubicación física.</li>
        <li><strong>Alertas Inteligentes:</strong> Indicadores visuales automáticos cuando el stock está por debajo del mínimo.</li>
        <li><strong>Descuento Automático:</strong> Al usar un repuesto en una orden, se descuenta del inventario general en tiempo real.</li>
    </ul>

    <h3>📄 Documentación Técnica</h3>
    <ul>
        <li><strong>Informes Técnicos (Laboratorio):</strong> Módulo independiente para diagnóstico profundo.</li>
        <li><strong>Checklist Industrial:</strong> Validación de componentes (Cable, Motor, Válvulas, Carbones).</li>
        <li><strong>Evidencia Fotográfica:</strong> Galería de imágenes del estado del equipo.</li>
        <li><strong>Generación PDF:</strong> Documentos vectoriales profesionales listos para imprimir y firmar.</li>
    </ul>

    <h3>🛡️ Seguridad y Roles (RBAC)</h3>
    <ul>
        <li><strong>Administrador:</strong> Acceso total a finanzas, configuración global y gestión de usuarios.</li>
        <li><strong>Técnico:</strong> Acceso operativo restringido (No ve costos de compra, ni facturación total, ni configuración).</li>
    </ul>

    <hr>

    <h2>💻 Stack Tecnológico</h2>
    <table>
        <thead>
            <tr>
                <th>Capa</th>
                <th>Tecnología</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Backend</strong></td>
                <td>Laravel 11/12</td>
                <td>Framework PHP robusto y seguro.</td>
            </tr>
            <tr>
                <td><strong>Lenguaje</strong></td>
                <td>PHP 8.3</td>
                <td>Última versión estable con JIT compiler.</td>
            </tr>
            <tr>
                <td><strong>Frontend</strong></td>
                <td>Tailwind CSS</td>
                <td>Framework de utilidades para diseño "Deep Obsidian".</td>
            </tr>
            <tr>
                <td><strong>UI Kit</strong></td>
                <td>DaisyUI + MaryUI</td>
                <td>Componentes visuales de alto nivel.</td>
            </tr>
            <tr>
                <td><strong>Base de Datos</strong></td>
                <td>MariaDB</td>
                <td>Motor SQL optimizado.</td>
            </tr>
            <tr>
                <td><strong>Servidor</strong></td>
                <td>Nginx</td>
                <td>Servidor web de alto rendimiento.</td>
            </tr>
        </tbody>
    </table>

    <hr>

    <h2>📦 Guía de Instalación (Despliegue)</h2>
    <p>Sigue estos pasos estrictos para desplegar en un entorno de producción (VPS/LXC/Proxmox).</p>

    <h3>1. Preparación del Código</h3>
    <pre><code>cd /var/www
git clone https://github.com/TU_USUARIO/techlife-system.git repair-app
cd repair-app</code></pre>

    <h3>2. Instalación de Dependencias</h3>
    <pre><code># Backend
composer install --optimize-autoloader --no-dev

# Frontend
npm install
npm run build</code></pre>

    <h3>3. Configuración de Entorno</h3>
    <pre><code>cp .env.example .env
nano .env</code></pre>
    <p><em>Edita las credenciales de base de datos dentro del archivo:</em></p>
    <pre><code>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=repair_db
DB_USERNAME=repair_user
DB_PASSWORD=tu_password_segura</code></pre>

    <h3>4. Inicialización</h3>
    <pre><code># Generar llave de encriptación
php artisan key:generate

# Crear enlace simbólico para fotos
php artisan storage:link

# Migrar base de datos (Estructura)
php artisan migrate --force</code></pre>

    <h3>5. Permisos (CRÍTICO)</h3>
    <pre><code>chown -R www-data:www-data /var/www/repair-app
chmod -R 775 /var/www/repair-app/storage
chmod -R 775 /var/www/repair-app/bootstrap/cache</code></pre>

    <hr>

    <h2>🔧 Configuración de Nginx</h2>
    <p>Crea el archivo: <code>/etc/nginx/sites-available/repair-app</code></p>

    <pre><code>server {
    listen 80;
    server_name tudominio.com; # O la IP de tu servidor
    root /var/www/repair-app/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # Permitir subida de fotos grandes
    client_max_body_size 16M;
}</code></pre>

    <p><strong>Activar sitio:</strong></p>
    <pre><code>ln -s /etc/nginx/sites-available/repair-app /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx</code></pre>

    <hr>

    <h2>👤 Primer Acceso (Crear Admin)</h2>
    <p>Como la base de datos está vacía, crea el primer usuario vía consola:</p>
    <pre><code>php artisan tinker</code></pre>
    <p>Ejecuta el siguiente comando en la consola interactiva:</p>
    <pre><code>\App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'admin@techlife.com',
    'password' => bcrypt('password123'),
    'role' => 'admin'
]);
exit</code></pre>

    <hr>

    <h2>🛠️ Solución de Problemas (Troubleshooting)</h2>

    <div class="alert">
        <strong>Importante:</strong> Si ves errores, prueba estos comandos antes de nada.
    </div>

    <h3>1. Error al imprimir PDF ("Imagick not installed")</h3>
    <pre><code>apt install php8.3-imagick
systemctl restart php8.3-fpm</code></pre>

    <h3>2. Error al subir fotos ("Payload too large")</h3>
    <p>Edita <code>/etc/php/8.3/fpm/php.ini</code> y ajusta:</p>
    <pre><code>upload_max_filesize = 16M
post_max_size = 16M</code></pre>
    <p>Reinicia PHP: <code>systemctl restart php8.3-fpm</code></p>

    <h3>3. Estilos rotos o pantalla blanca (Login/Register)</h3>
    <p>Fuerza la recompilación de los estilos y limpia la caché:</p>
    <pre><code>rm -rf public/build
npm run build
php artisan optimize:clear
php artisan view:clear</code></pre>

    <hr>
    
    <p style="text-align: center; color: #666; margin-top: 50px;">
        &copy; 2025 TechLife Solutions. Desarrollado para alta eficiencia.
    </p>

</div>

</body>
</html>
