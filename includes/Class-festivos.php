<?php
// includes/Class-festivos.php
require_once 'conn-db.php';

// Incluir autoload de Composer para Guzzle
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

class Festivos {
    private $conn;
    private $table_name = "festivos_cache";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Consultar días festivos desde la API
     * @param string $pais Código del país (ej: 'CO', 'US')
     * @param int $anio Año a consultar
     * @return array Array con los días festivos
     */
    public function consultarFestivosAPI($pais, $anio) {
        try {
            // Obtener configuración de la API
            $api_url = $this->obtenerConfiguracion('festivos_api_url');
            if (!$api_url) {
                $api_url = 'https://date.nager.at/api/v3/PublicHolidays';
            }

            // Construir URL completa
            $url = $api_url . '/' . $anio . '/' . $pais;

            // Usar Guzzle HTTP si está disponible
            if (class_exists('GuzzleHttp\Client')) {
                $client = new GuzzleHttp\Client();
                $response = $client->request('GET', $url);

                if ($response->getStatusCode() === 200) {
                    $festivos = json_decode($response->getBody(), true);
                    return $this->procesarFestivosAPI($festivos, $anio);
                }
            } else {
                // Fallback usando cURL si Guzzle no está disponible
                return $this->consultarFestivosCurl($url, $anio);
            }

        } catch (Exception $e) {
            error_log("Error consultando API de festivos: " . $e->getMessage());
            return [];
        }

        return [];
    }

    /**
     * Consultar días festivos usando cURL (fallback)
     * @param string $url URL de la API
     * @param int $anio Año
     * @return array Array con los días festivos
     */
    private function consultarFestivosCurl($url, $anio) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200 && $response) {
            $festivos = json_decode($response, true);
            return $this->procesarFestivosAPI($festivos, $anio);
        }

        return [];
    }

    /**
     * Procesar respuesta de la API de festivos
     * @param array $festivos Respuesta de la API
     * @param int $anio Año
     * @return array Array procesado de festivos
     */
    private function procesarFestivosAPI($festivos, $anio) {
        $procesados = [];

        foreach ($festivos as $festivo) {
            if (isset($festivo['date'])) {
                $procesados[] = [
                    'fecha' => $festivo['date'],
                    'nombre' => $festivo['localName'] ?? $festivo['name'] ?? 'Día festivo',
                    'tipo' => 'festivo',
                    'anio' => $anio
                ];
            }
        }

        return $procesados;
    }

    /**
     * Guardar festivos en cache
     * @param array $festivos Array de festivos
     * @param string $pais Código del país
     * @param int $anio Año
     */
    public function guardarFestivosCache($festivos, $pais, $anio) {
        try {
            // Usar transacción para asegurar atomicidad
            $this->conn->beginTransaction();

            // Limpiar cache anterior para este país/año
            $query_delete = "DELETE FROM festivos_cache WHERE pais = :pais AND anio = :anio";
            $stmt_delete = $this->conn->prepare($query_delete);
            $stmt_delete->bindParam(':pais', $pais);
            $stmt_delete->bindParam(':anio', $anio);
            $stmt_delete->execute();

            // Insertar nuevos festivos usando INSERT IGNORE para evitar duplicados
            $query_insert = "INSERT IGNORE INTO festivos_cache (fecha, nombre, tipo, pais, anio) VALUES (:fecha, :nombre, :tipo, :pais, :anio)";
            $stmt_insert = $this->conn->prepare($query_insert);

            foreach ($festivos as $festivo) {
                $stmt_insert->bindParam(':fecha', $festivo['fecha']);
                $stmt_insert->bindParam(':nombre', $festivo['nombre']);
                $stmt_insert->bindParam(':tipo', $festivo['tipo']);
                $stmt_insert->bindParam(':pais', $pais);
                $stmt_insert->bindParam(':anio', $anio);
                $stmt_insert->execute();
            }

            $this->conn->commit();

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error guardando festivos en cache: " . $e->getMessage());
        }
    }

    /**
     * Obtener festivos desde cache
     * @param string $pais Código del país
     * @param int $anio Año
     * @return array Array de festivos
     */
    public function obtenerFestivosCache($pais, $anio) {
        $query = "SELECT fecha, nombre, tipo FROM festivos_cache
                  WHERE pais = :pais AND anio = :anio
                  ORDER BY fecha ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pais', $pais);
        $stmt->bindParam(':anio', $anio);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar si una fecha es festiva
     * @param string $fecha Fecha en formato Y-m-d
     * @param string $pais Código del país (opcional)
     * @return bool True si es festivo
     */
    public function esFestivo($fecha, $pais = null) {
        if (!$pais) {
            $pais = $this->obtenerConfiguracion('festivos_pais');
        }

        if (!$pais) {
            return false;
        }

        $anio = date('Y', strtotime($fecha));

        // Verificar en cache primero
        $query = "SELECT COUNT(*) as total FROM festivos_cache
                  WHERE pais = :pais AND anio = :anio AND fecha = :fecha";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pais', $pais);
        $stmt->bindParam(':anio', $anio);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['total'] > 0) {
            return true;
        }

        // Si no está en cache y la consulta automática está habilitada, consultar API
        if ($this->obtenerConfiguracion('festivos_consulta_automatica') === '1') {
            // Verificar nuevamente si otro proceso ya lo agregó mientras esperábamos
            $query_check = "SELECT COUNT(*) as total FROM festivos_cache
                           WHERE pais = :pais AND anio = :anio AND fecha = :fecha";
            $stmt_check = $this->conn->prepare($query_check);
            $stmt_check->bindParam(':pais', $pais);
            $stmt_check->bindParam(':anio', $anio);
            $stmt_check->bindParam(':fecha', $fecha);
            $stmt_check->execute();
            $result_check = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($result_check['total'] == 0) {
                $festivos = $this->consultarFestivosAPI($pais, $anio);
                if (!empty($festivos)) {
                    $this->guardarFestivosCache($festivos, $pais, $anio);
                    // Verificar nuevamente después de actualizar cache
                    return $this->esFestivo($fecha, $pais);
                }
            } else {
                // Otro proceso ya lo agregó, verificar nuevamente
                return ($result_check['total'] > 0);
            }
        }

        return false;
    }

    /**
     * Actualizar festivos mensualmente
     * Consulta y guarda los festivos del año actual si no están en cache
     */
    public function actualizarFestivosMensual() {
        $pais = $this->obtenerConfiguracion('festivos_pais');
        $anio_actual = date('Y');

        if (!$pais) {
            error_log("No se ha configurado el país para festivos");
            return false;
        }

        // Verificar si ya tenemos festivos para este año
        $query = "SELECT COUNT(*) as total FROM festivos_cache WHERE pais = :pais AND anio = :anio";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pais', $pais);
        $stmt->bindParam(':anio', $anio_actual);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['total'] == 0) {
            // No hay festivos en cache, consultar API
            $festivos = $this->consultarFestivosAPI($pais, $anio_actual);
            if (!empty($festivos)) {
                $this->guardarFestivosCache($festivos, $pais, $anio_actual);
                error_log("Festivos actualizados para el año $anio_actual");
                return true;
            } else {
                error_log("Error al consultar festivos para el año $anio_actual");
                return false;
            }
        }

        return true; // Ya están actualizados
    }

    /**
     * Obtener configuración del sistema
     * @param string $clave Clave de configuración
     * @return string|null Valor de configuración
     */
    public function obtenerConfiguracion($clave) {
        $query = "SELECT valor FROM configuracion_sistema WHERE clave = :clave";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clave', $clave);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['valor'] : null;
    }

    /**
     * Obtener festivos para un rango de fechas
     * @param string $fecha_inicio Fecha inicio
     * @param string $fecha_fin Fecha fin
     * @param string $pais Código del país (opcional)
     * @return array Array de fechas festivas
     */
    public function obtenerFestivosRango($fecha_inicio, $fecha_fin, $pais = null) {
        if (!$pais) {
            $pais = $this->obtenerConfiguracion('festivos_pais');
        }

        if (!$pais) {
            return [];
        }

        $query = "SELECT fecha, nombre FROM festivos_cache
                  WHERE pais = :pais AND fecha BETWEEN :fecha_inicio AND :fecha_fin
                  ORDER BY fecha ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pais', $pais);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>