<?php
// includes/Class-webhook.php
require_once 'conn-db.php';
require_once 'Class-configuracion.php';

class WebhookProjectDashboard {
    private $config;

    public function __construct() {
        $this->config = new Configuracion();
    }

    /**
     * Enviar datos a ProjectDashboard via webhook
     * @param array $registros Array de registros a enviar
     * @return array Resultado del envío
     */
    public function enviarDatos($registros) {
        // Verificar si está habilitado
        $habilitado = $this->config->obtenerValor('projectdashboard_habilitado', false);
        if (!$habilitado) {
            return [
                'success' => false,
                'message' => 'La sincronización con ProjectDashboard está deshabilitada'
            ];
        }

        // Obtener URL del webhook
        $webhook_url = $this->config->obtenerValor('projectdashboard_url', '');
        if (empty($webhook_url)) {
            return [
                'success' => false,
                'message' => 'No se ha configurado la URL del webhook'
            ];
        }

        // Obtener token de autenticación
        $token = $this->config->obtenerValor('projectdashboard_webhook_token', '');

        // Preparar datos en formato esperado
        $datos_envio = [];
        foreach ($registros as $registro) {
            // Convertir horas a formato decimal con coma (ej: 3,5 para 3 horas y 30 minutos)
            $tiempo_ordinario_decimal = str_replace('.', ',', number_format($registro['tiempo_ordinario'], 1));
            $tiempo_extra_decimal = str_replace('.', ',', number_format($registro['tiempo_extra'], 1));
            // Total pagado sin puntuación: 48806 en vez de 48.806
            $total_pagado_formateado = number_format($registro['total_pagado'], 0, '', '');

            $datos_envio[] = [
                'marca_temporal' => date('d/m/Y H:i:s'),
                'proyecto_numero' => $registro['proyecto_numero'],
                'fecha' => date('d/m/Y', strtotime($registro['fecha'])),
                'nombre_empleado' => $registro['nombre_empleado'],
                'cargo' => $registro['cargo'],
                'area_trabajo' => $registro['area_trabajo'],
                'tiempo_ordinario' => $tiempo_ordinario_decimal,
                'tiempo_extra' => $tiempo_extra_decimal,
                'total_pagado' => $total_pagado_formateado,
                // Metadata adicional
                'metadata' => [
                    'usuario_id' => $registro['usuario_id'],
                    'orden_produccion_id' => $registro['orden_produccion_id'],
                    'tipo' => $registro['tipo'],
                    'tiempo_ordinario_numerico' => $registro['tiempo_ordinario'],
                    'tiempo_extra_numerico' => $registro['tiempo_extra'],
                    'total_pagado_numerico' => $registro['total_pagado'],
                    'detalles_normales_ids' => array_column($registro['detalles_normales'] ?? [], 'id'),
                    'detalles_extras_ids' => array_column($registro['detalles_extras'] ?? [], 'id')
                ]
            ];
        }

        // Preparar payload
        $payload = json_encode([
            'registros' => $datos_envio,
            'sistema_origen' => 'TIME_PRODUCTION',
            'timestamp' => date('c')
        ]);

        // Configurar headers
        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload)
        ];

        if (!empty($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        // Enviar via cURL
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Para desarrollo, en producción debe ser true

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Procesar respuesta
        if ($curl_error) {
            return [
                'success' => false,
                'message' => 'Error de conexión: ' . $curl_error,
                'http_code' => 0
            ];
        }

        // Decodificar respuesta
        $response_data = json_decode($response, true);

        if ($http_code >= 200 && $http_code < 300) {
            return [
                'success' => true,
                'message' => 'Datos enviados correctamente',
                'http_code' => $http_code,
                'registros_enviados' => count($datos_envio),
                'respuesta' => $response_data
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al enviar datos (HTTP ' . $http_code . ')',
                'http_code' => $http_code,
                'respuesta' => $response_data ?? $response
            ];
        }
    }

    /**
     * Probar conexión con el webhook
     * @return array Resultado de la prueba
     */
    public function probarConexion() {
        $webhook_url = $this->config->obtenerValor('projectdashboard_url', '');
        if (empty($webhook_url)) {
            return [
                'success' => false,
                'message' => 'No se ha configurado la URL del webhook'
            ];
        }

        $token = $this->config->obtenerValor('projectdashboard_webhook_token', '');

        // Enviar ping
        $payload = json_encode([
            'ping' => true,
            'sistema_origen' => 'TIME_PRODUCTION',
            'timestamp' => date('c')
        ]);

        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload)
        ];

        if (!empty($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            return [
                'success' => false,
                'message' => 'Error de conexión: ' . $curl_error
            ];
        }

        if ($http_code >= 200 && $http_code < 300) {
            return [
                'success' => true,
                'message' => 'Conexión exitosa (HTTP ' . $http_code . ')',
                'http_code' => $http_code
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error de conexión (HTTP ' . $http_code . ')',
                'http_code' => $http_code,
                'respuesta' => $response
            ];
        }
    }
}
