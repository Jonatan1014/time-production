use horas_produccion_db;
-- =====================================================
-- TABLA: CACHE DE DÍAS FESTIVOS
-- Almacena los días festivos obtenidos desde la API
-- =====================================================
CREATE TABLE IF NOT EXISTS festivos_cache (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fecha DATE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    tipo ENUM('festivo', 'puente') DEFAULT 'festivo',
    pais VARCHAR(5) NOT NULL,
    anio INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_festivo (pais, anio, fecha),
    INDEX idx_pais_anio (pais, anio),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO configuracion_sistema (clave, valor, tipo, descripcion, categoria) VALUES
('factor_extra_diurna', '1.25', 'decimal', 'Factor multiplicador para horas extras diurnas (HED: 1.25x)', 'costos'),
('factor_extra_nocturna', '1.35', 'decimal', 'Factor multiplicador para horas extras nocturnas (HEN: 1.35x)', 'costos'),
('factor_fin_semana_diurno', '2.1', 'decimal', 'Factor multiplicador para horas extras fin de semana diurnas (HEFD: 2.1x)', 'costos'),
('factor_fin_semana_nocturno', '2.5', 'decimal', 'Factor multiplicador para horas extras fin de semana nocturnas (HEFN: 2.5x)', 'costos'),
('factor_festivo_diurno', '2.1', 'decimal', 'Factor multiplicador para horas extras días festivos diurnas (HEFD: 2.1x)', 'costos'),
('factor_festivo_nocturno', '2.5', 'decimal', 'Factor multiplicador para horas extras días festivos nocturnas (HEFN: 2.5x)', 'costos'),
('recargo_nocturno', '1.35', 'decimal', 'Recargo adicional para horas nocturnas (1.35x)', 'costos'),
('mostrar_costos', '1', 'booleano', 'Mostrar información de costos en reportes', 'costos'),
('festivos_pais', 'CO', 'texto', 'Código ISO del país para consultar días festivos', 'integraciones'),
('festivos_api_url', 'https://date.nager.at/api/v3/PublicHolidays', 'texto', 'URL base de la API de días festivos', 'integraciones'),
('festivos_consulta_automatica', '1', 'booleano', 'Consultar automáticamente días festivos para calcular recargos', 'integraciones')
ON DUPLICATE KEY UPDATE valor=VALUES(valor);