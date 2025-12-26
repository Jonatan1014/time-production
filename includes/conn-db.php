<?php
require_once ('settings-db.php');
require_once ('license-config.php');

// ============ VALIDACIÓN DE LICENCIA ============
// IMPORTANTE: Este sistema valida la licencia antes de permitir
// la conexión a la base de datos. NO ELIMINAR ni COMENTAR.
LicenseValidator::validateLicense();
// ================================================

if (!class_exists('Database')) {
    class Database {	
        protected $conn_db;
        
        public function __construct() {		
            try {  
                // Usar las constantes definidas en settings.php
                $this->conn_db = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
                
                // Configuraciones adicionales (si necesitas algo específico)
                $this->conn_db->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
                
                return $this->conn_db;
                
            } catch(PDOException $e) {				
                // Manejo mejorado de errores
                error_log("Error de conexión DB: " . $e->getMessage());
                
                if (defined('APP_ENV') && APP_ENV === 'development') {
                    die("Error al conectar a la DB: " . $e->getMessage());
                } else {
                    die("Error al conectar con la base de datos");
                }
            }			
        }
        
        // Método para obtener la conexión
        public function getConnection() {
            return $this->conn_db;
        }
    }
}