/**
 * Sistema de Licencia Temporal para Sysmaint
 * Este archivo gestiona la validación de la licencia del sistema
 * 
 * IMPORTANTE: Este archivo trabaja en conjunto con license-config.php
 * para validar la licencia tanto en cliente como en servidor
 */

(function() {
    'use strict';

    // Configuración de la licencia (MODIFICAR AQUÍ)
    const LICENSE_CONFIG = {
        // Fecha de inicio de la licencia (formato: YYYY-MM-DD)
        startDate: '2025-11-19',
        
        // Días de licencia (MODIFICAR ESTE VALOR para cambiar la duración)
        licenseDays: 60,
        
        // Clave de verificación (NO MODIFICAR - se genera automáticamente)
        verificationKey: 'SYS_' + btoa('2025-11-19_30').substring(0, 16)
    };

    /**
     * Calcula la fecha de expiración de la licencia
     */
    function getExpirationDate() {
        const start = new Date(LICENSE_CONFIG.startDate);
        const expiration = new Date(start);
        expiration.setDate(expiration.getDate() + LICENSE_CONFIG.licenseDays);
        return expiration;
    }

    /**
     * Verifica si la licencia está activa
     */
    function isLicenseValid() {
        const now = new Date();
        const expiration = getExpirationDate();
        
        // Verificar que la fecha actual esté dentro del período de licencia
        return now <= expiration;
    }

    /**
     * Calcula los días restantes de licencia
     */
    function getDaysRemaining() {
        const now = new Date();
        const expiration = getExpirationDate();
        const diffTime = expiration - now;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays > 0 ? diffDays : 0;
    }

    /**
     * Genera el token de verificación para el servidor
     */
    function generateVerificationToken() {
        const timestamp = new Date().getTime();
        const seed = LICENSE_CONFIG.verificationKey + timestamp;
        return btoa(seed).substring(0, 32);
    }

    /**
     * Bloquea el acceso al sistema cuando la licencia expira
     */
    function blockSystem() {
        // Crear overlay de bloqueo
        const overlay = document.createElement('div');
        overlay.id = 'license-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 99999;
            display: flex;
            justify-content: center;
            align-items: center;
        `;

        const message = document.createElement('div');
        message.style.cssText = `
            background: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        `;

        const expirationDate = getExpirationDate();
        
        message.innerHTML = `
            <div style="color: #dc3545; font-size: 60px; margin-bottom: 20px;">
                <i class="mdi mdi-lock-alert"></i>
            </div>
            <h2 style="color: #333; margin-bottom: 15px;">Licencia Expirada</h2>
            <p style="color: #666; font-size: 16px; line-height: 1.6;">
                La licencia de <strong>Sysmaint</strong> ha expirado.<br>
                Fecha de expiración: <strong>${expirationDate.toLocaleDateString('es-ES')}</strong>
            </p>
            <p style="color: #999; font-size: 14px; margin-top: 20px;">
                Por favor, contacte al administrador del sistema para renovar la licencia.
            </p>
            <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                <p style="color: #666; font-size: 12px; margin: 0;">
                    <strong>Código de licencia:</strong> ${LICENSE_CONFIG.verificationKey}
                </p>
            </div>
        `;

        overlay.appendChild(message);
        document.body.appendChild(overlay);

        // Deshabilitar todos los enlaces e inputs
        document.querySelectorAll('a, button, input, select, textarea').forEach(el => {
            el.style.pointerEvents = 'none';
            el.disabled = true;
        });

        // Prevenir navegación
        window.onbeforeunload = function() {
            return 'La licencia del sistema ha expirado.';
        };
    }

    /**
     * Muestra advertencia cuando quedan pocos días
     */
    function showWarning() {
        const daysLeft = getDaysRemaining();
        
        if (daysLeft <= 7 && daysLeft > 0) {
            const warningBar = document.createElement('div');
            warningBar.id = 'license-warning';
            warningBar.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                background: linear-gradient(135deg, #f39c12, #e67e22);
                color: white;
                padding: 12px 20px;
                text-align: center;
                z-index: 9999;
                font-weight: 500;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            `;

            warningBar.innerHTML = `
                <i class="mdi mdi-alert-circle-outline"></i>
                <strong>Advertencia:</strong> La licencia del sistema expirará en ${daysLeft} día${daysLeft !== 1 ? 's' : ''}.
                Por favor, renueve la licencia antes del ${getExpirationDate().toLocaleDateString('es-ES')}.
                <button onclick="this.parentElement.remove()" style="
                    background: rgba(255,255,255,0.2);
                    border: 1px solid rgba(255,255,255,0.3);
                    color: white;
                    padding: 5px 15px;
                    border-radius: 4px;
                    margin-left: 15px;
                    cursor: pointer;
                ">Entendido</button>
            `;

            // Ajustar el body para que no se sobreponga el contenido
            document.body.style.paddingTop = '50px';
            document.body.insertBefore(warningBar, document.body.firstChild);
        }
    }

    /**
     * Inyecta el token de verificación en todos los formularios
     */
    function injectVerificationToken() {
        const token = generateVerificationToken();
        
        // Agregar token a todos los formularios
        document.querySelectorAll('form').forEach(form => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'license_token';
            input.value = token;
            form.appendChild(input);
        });

        // Agregar token a todas las peticiones AJAX (si usan fetch)
        if (window.fetch) {
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                if (args[1] && args[1].body) {
                    if (args[1].body instanceof FormData) {
                        args[1].body.append('license_token', token);
                    }
                }
                return originalFetch.apply(this, args);
            };
        }
    }

    /**
     * Valida la integridad del código
     */
    function validateIntegrity() {
        // Verificar que no se haya manipulado el archivo
        const expectedKey = 'SYS_' + btoa(LICENSE_CONFIG.startDate + '_' + LICENSE_CONFIG.licenseDays).substring(0, 16);
        
        if (LICENSE_CONFIG.verificationKey !== expectedKey) {
            console.error('License integrity check failed');
            blockSystem();
            return false;
        }
        return true;
    }

    /**
     * Inicializa el sistema de licencias
     */
    function initLicenseSystem() {
        // Validar integridad
        if (!validateIntegrity()) {
            return;
        }

        // Verificar estado de la licencia
        if (!isLicenseValid()) {
            console.warn('License expired on:', getExpirationDate());
            blockSystem();
            return;
        }

        // Mostrar advertencia si quedan pocos días
        showWarning();

        // Inyectar token en formularios
        injectVerificationToken();

        // Mostrar info de licencia en consola (solo desarrollo)
        console.log('%c🔐 License Status', 'color: #28a745; font-size: 14px; font-weight: bold;');
        console.log('Days remaining:', getDaysRemaining());
        console.log('Expiration date:', getExpirationDate().toLocaleDateString('es-ES'));
    }

    /**
     * Prevenir manipulación del código
     */
    function preventTampering() {
        // Detectar si se intenta modificar la fecha del sistema
        const originalDate = Date;
        let lastValidDate = new originalDate();

        Object.defineProperty(window, 'Date', {
            get: function() {
                const now = new originalDate();
                // Si la fecha retrocede más de 1 día, es sospechoso
                if (now < lastValidDate - 86400000) {
                    blockSystem();
                }
                lastValidDate = now;
                return originalDate;
            },
            configurable: false
        });

        // Prevenir apertura de DevTools (opcional - puede ser molesto en desarrollo)
        // Descomentar si se desea activar:
        /*
        setInterval(function() {
            const threshold = 160;
            if (window.outerWidth - window.innerWidth > threshold || 
                window.outerHeight - window.innerHeight > threshold) {
                blockSystem();
            }
        }, 1000);
        */
    }

    /**
     * Verificación periódica de la licencia
     */
    function startPeriodicCheck() {
        // Verificar cada 5 minutos
        setInterval(function() {
            if (!isLicenseValid()) {
                blockSystem();
            }
        }, 300000); // 5 minutos
    }

    // Ejecutar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initLicenseSystem();
            preventTampering();
            startPeriodicCheck();
        });
    } else {
        initLicenseSystem();
        preventTampering();
        startPeriodicCheck();
    }

    // Exponer funciones útiles globalmente (solo para debugging)
    window.LicenseSystem = {
        getDaysRemaining: getDaysRemaining,
        getExpirationDate: getExpirationDate,
        isValid: isLicenseValid,
        // Función oculta para renovar licencia (solo para administrador)
        extend: function(newDays, adminKey) {
            if (adminKey === btoa('SYSMAINT_ADMIN_2025')) {
                LICENSE_CONFIG.licenseDays += newDays;
                LICENSE_CONFIG.verificationKey = 'SYS_' + btoa(LICENSE_CONFIG.startDate + '_' + LICENSE_CONFIG.licenseDays).substring(0, 16);
                console.log('License extended by', newDays, 'days');
                location.reload();
            }
        }
    };

})();

/**
 * INSTRUCCIONES DE USO:
 * =====================
 * 
 * 1. Para cambiar los días de licencia, modifica el valor de 'licenseDays' en LICENSE_CONFIG
 *    Ejemplo: licenseDays: 60  (para 60 días)
 * 
 * 2. Incluye este archivo en todas las páginas del sistema:
 *    <script src="assets/js/license-system.js"></script>
 * 
 * 3. También debes incluir license-config.php en el servidor para validación completa
 * 
 * 4. Para ver el estado de la licencia en consola:
 *    LicenseSystem.getDaysRemaining()
 *    LicenseSystem.getExpirationDate()
 * 
 * 5. Para extender la licencia (solo administrador):
 *    LicenseSystem.extend(30, btoa('SYSMAINT_ADMIN_2025'))
 */
