<?php
/**
 * Sistema de Licencia del Servidor - Sysmaint
 * Este archivo valida la licencia en el lado del servidor
 * 
 * IMPORTANTE: Incluir este archivo en includes/conn-db.php
 * para validar antes de conectar a la base de datos
 */

class LicenseValidator {
    // ============ CONFIGURACIÓN DE LICENCIA ============
    // MODIFICAR ESTOS VALORES PARA CONFIGURAR LA LICENCIA
    
    private const LICENSE_START_DATE = '2025-11-19'; // Fecha de inicio (YYYY-MM-DD)
    private const LICENSE_DAYS = 60;                  // Días de licencia
    
    // ===================================================
    
    private const VERIFICATION_KEY = 'SYSMAINT_LICENSE_2025';
    private static $licenseChecked = false;
    
    /**
     * Valida la licencia del sistema
     * @return bool True si la licencia es válida, False si expiró
     */
    public static function validateLicense() {
        // Evitar múltiples validaciones
        if (self::$licenseChecked) {
            return true;
        }
        
        // Calcular fecha de expiración
        $startDate = new DateTime(self::LICENSE_START_DATE);
        $expirationDate = clone $startDate;
        $expirationDate->modify('+' . self::LICENSE_DAYS . ' days');
        
        $now = new DateTime();
        
        // Verificar si la licencia está activa
        if ($now > $expirationDate) {
            self::blockSystemAccess($expirationDate);
            return false;
        }
        
        // Verificar token de cliente (si existe)
        if (isset($_POST['license_token']) || isset($_GET['license_token'])) {
            $token = $_POST['license_token'] ?? $_GET['license_token'];
            if (!self::validateToken($token)) {
                self::blockSystemAccess($expirationDate, 'Token inválido');
                return false;
            }
        }
        
        // Mostrar advertencia si quedan pocos días
        self::checkWarning($expirationDate, $now);
        
        self::$licenseChecked = true;
        return true;
    }
    
    /**
     * Valida el token enviado desde el cliente
     */
    private static function validateToken($token) {
        // Validación básica del token
        return strlen($token) >= 16 && strlen($token) <= 64;
    }
    
    /**
     * Verifica si debe mostrar advertencia de expiración próxima
     */
    private static function checkWarning($expirationDate, $now) {
        $interval = $now->diff($expirationDate);
        $daysRemaining = $interval->days;
        
        // Si quedan 7 días o menos, guardar advertencia en sesión
        if ($daysRemaining <= 7 && $daysRemaining > 0) {
            if (session_status() === PHP_SESSION_NONE) {
                @session_start();
            }
            $_SESSION['license_warning'] = [
                'days_remaining' => $daysRemaining,
                'expiration_date' => $expirationDate->format('Y-m-d'),
                'show' => true
            ];
        }
    }
    
    /**
     * Bloquea el acceso al sistema cuando la licencia expira
     */
    private static function blockSystemAccess($expirationDate, $reason = 'Licencia expirada') {
        // Si es una petición AJAX, devolver JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error' => 'LICENSE_EXPIRED',
                'message' => $reason,
                'expiration_date' => $expirationDate->format('Y-m-d')
            ]);
            exit;
        }
        
        // Mostrar página de error HTML
        http_response_code(403);
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Licencia Expirada - Sysmaint</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    padding: 20px;
                }
                .container {
                    background: white;
                    border-radius: 20px;
                    padding: 60px 40px;
                    max-width: 600px;
                    width: 100%;
                    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                    text-align: center;
                    animation: slideIn 0.5s ease-out;
                }
                @keyframes slideIn {
                    from { opacity: 0; transform: translateY(-30px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .icon {
                    font-size: 80px;
                    color: #dc3545;
                    margin-bottom: 20px;
                }
                h1 {
                    color: #333;
                    font-size: 32px;
                    margin-bottom: 15px;
                    font-weight: 600;
                }
                .reason {
                    color: #666;
                    font-size: 18px;
                    margin-bottom: 10px;
                }
                .date {
                    color: #999;
                    font-size: 16px;
                    margin-bottom: 30px;
                }
                .info-box {
                    background: #f8f9fa;
                    border-left: 4px solid #dc3545;
                    padding: 20px;
                    margin: 30px 0;
                    text-align: left;
                    border-radius: 8px;
                }
                .info-box p {
                    color: #666;
                    line-height: 1.8;
                    margin-bottom: 10px;
                }
                .info-box strong {
                    color: #333;
                }
                .license-code {
                    background: #e9ecef;
                    padding: 15px;
                    border-radius: 8px;
                    margin-top: 20px;
                    font-family: 'Courier New', monospace;
                    font-size: 14px;
                    color: #495057;
                    word-break: break-all;
                }
                .footer {
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 2px solid #e9ecef;
                    color: #999;
                    font-size: 14px;
                }
                .logo {
                    font-size: 24px;
                    font-weight: bold;
                    color: #667eea;
                    margin-bottom: 30px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="logo">SYSMAINT</div>
                <div class="icon">🔒</div>
                <h1>Acceso Denegado</h1>
                <p class="reason"><?php echo htmlspecialchars($reason); ?></p>
                <p class="date">Fecha de expiración: <strong><?php echo $expirationDate->format('d/m/Y'); ?></strong></p>
                
                <div class="info-box">
                    <p><strong>⚠️ Sistema bloqueado</strong></p>
                    <p>El sistema <strong>Sysmaint</strong> ha dejado de funcionar porque la licencia ha expirado.</p>
                    <p>Para reactivar el sistema, contacte al proveedor del software para renovar la licencia.</p>
                    
                    <div class="license-code">
                        <strong>Código de licencia:</strong><br>
                        <?php echo strtoupper(md5(self::VERIFICATION_KEY . self::LICENSE_START_DATE)); ?>
                    </div>
                </div>

                <div class="info-box" style="border-left-color: #17a2b8;">
                    <p><strong>📞 Información de contacto</strong></p>
                    <p>Para renovar su licencia o solicitar soporte técnico:</p>
                    <p><strong>Email:</strong> jcantillocompany@gmail.com</p>
                    <p><strong>Teléfono:</strong> +57 317 446 6432</p>
                </div>

                <div class="footer">
                    <p>Sysmaint - Sistema de Gestión de Mantenimiento Vehicular</p>
                    <p>&copy; 2025 Todos los derechos reservados @JCantillo</p>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
    
    /**
     * Obtiene información de la licencia
     * @return array Información de la licencia
     */
    public static function getLicenseInfo() {
        $startDate = new DateTime(self::LICENSE_START_DATE);
        $expirationDate = clone $startDate;
        $expirationDate->modify('+' . self::LICENSE_DAYS . ' days');
        $now = new DateTime();
        
        $interval = $now->diff($expirationDate);
        $daysRemaining = $interval->invert ? 0 : $interval->days;
        
        return [
            'start_date' => $startDate->format('Y-m-d'),
            'expiration_date' => $expirationDate->format('Y-m-d'),
            'total_days' => self::LICENSE_DAYS,
            'days_remaining' => $daysRemaining,
            'is_valid' => $now <= $expirationDate,
            'is_expired' => $now > $expirationDate
        ];
    }
    
    /**
     * Extiende la licencia (solo para uso administrativo)
     * @param int $additionalDays Días adicionales a agregar
     * @param string $adminKey Clave de administrador
     * @return bool True si se extendió correctamente
     */
    public static function extendLicense($additionalDays, $adminKey) {
        // Verificar clave de administrador
        $expectedKey = md5('SYSMAINT_ADMIN_' . date('Y'));
        
        if ($adminKey !== $expectedKey) {
            return false;
        }
        
        // NOTA: Esta función solo sirve para debug
        // En producción, deberías modificar manualmente LICENSE_DAYS en este archivo
        return true;
    }
}

/**
 * INSTRUCCIONES DE USO:
 * =====================
 * 
 * 1. Incluir este archivo en includes/conn-db.php ANTES de crear la conexión:
 *    require_once 'license-config.php';
 *    LicenseValidator::validateLicense();
 * 
 * 2. Para cambiar la duración de la licencia:
 *    - Modificar LICENSE_START_DATE con la fecha de inicio
 *    - Modificar LICENSE_DAYS con el número de días deseado
 * 
 * 3. Para obtener información de la licencia:
 *    $info = LicenseValidator::getLicenseInfo();
 *    echo "Días restantes: " . $info['days_remaining'];
 * 
 * 4. El sistema bloqueará automáticamente el acceso cuando expire la licencia
 */
