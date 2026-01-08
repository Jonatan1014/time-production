# Sistema de Cálculo de Costos - Legislación Laboral Colombiana

## 📋 Resumen

El sistema ahora calcula automáticamente los costos de horas trabajadas según la **legislación laboral colombiana**, detectando:

- ✅ Días festivos (desde API o base de datos)
- ✅ Días dominicales (domingos)
- ✅ Horarios diurnos vs nocturnos
- ✅ Tipos de horas extras con sus respectivos recargos

## 🎯 Factores Aplicados

### Horas Regulares

| Tipo | Factor | Descripción |
|------|--------|-------------|
| **Ordinaria regular** | `1.0x` | Lunes a sábado, horario diurno normal |
| **Ordinaria nocturna** | `1.35x` | Lunes a sábado, horario nocturno (21:00-06:00) |
| **Dominical regular** | `1.75x` | Domingo o festivo, horario diurno |
| **Dominical nocturna** | `2.1x` | Domingo o festivo, horario nocturno |

### Horas Extras

| Tipo | Factor | Descripción |
|------|--------|-------------|
| **Extra diurna ordinaria** | `1.25x` | Lunes a sábado, día (06:00-21:00) |
| **Extra nocturna ordinaria** | `1.75x` | Lunes a sábado, noche (21:00-06:00) |
| **Extra dominical diurna** | `2.0x` | Domingo o festivo, día |
| **Extra dominical nocturna** | `2.5x` | Domingo o festivo, noche |

## 🔧 Configuración

### Horarios (Base de datos)

Los horarios se configuran en la tabla `configuracion_sistema`:

```sql
-- Horario diurno: 06:00 a 21:00
hora_diurna_inicio = '06:00'
hora_diurna_fin = '21:00'
```

### Festivos

Los días festivos se consultan automáticamente desde:
- **API Nager.Date**: https://date.nager.at/api/v3/PublicHolidays/{año}/CO
- **Caché local**: Tabla `festivos_cache`

Para actualizar festivos:
```bash
# Manual (admin)
navegador -> Configuración -> Días Festivos -> Consultar año 2026

# Automático (cron)
curl "https://tudominio.com/time-production/cron-festivos.php?token=TOKEN"
```

## 💻 Ejemplos de Uso

### Ejemplo 1: Calcular costo de horas normales

```php
require_once 'includes/Class-costos.php';

$Costos = new Costos();

// Lunes 6 enero 2026 - 8 horas ordinarias
$resultado = $Costos->calcularCostoHorasNormales(
    $usuario_id = 1,
    $horas = 8.0,
    $fecha = '2026-01-06'
);

// Resultado:
// [
//     'valor_hora_base' => 5000,
//     'tipo_hora' => 'ordinaria_regular',
//     'factor' => 1.0,
//     'valor_hora' => 5000,
//     'horas' => 8.0,
//     'costo_total' => 40000,  // $40,000 COP
//     'es_diurna' => true,
//     'es_dominical' => false,
//     'es_festivo' => false
// ]
```

### Ejemplo 2: Calcular costo de horas en domingo

```php
// Domingo 11 enero 2026 - 8 horas dominicales
$resultado = $Costos->calcularCostoHorasNormales(
    $usuario_id = 1,
    $horas = 8.0,
    $fecha = '2026-01-11'  // Domingo
);

// Resultado:
// [
//     'tipo_hora' => 'dominical_regular',
//     'factor' => 1.75,
//     'costo_total' => 70000,  // $70,000 COP (1.75x)
//     'es_dominical' => true
// ]
```

### Ejemplo 3: Calcular costo de horas extras diurnas

```php
// Lunes - hora extra diurna (18:00-20:00)
$resultado = $Costos->calcularCostoHorasExtras(
    $usuario_id = 1,
    $horas = 2.0,
    $hora_inicio = '18:00',
    $hora_fin = '20:00',
    $fecha = '2026-01-06'  // Lunes
);

// Resultado:
// [
//     'tipo_recargo' => 'extra_diurna',
//     'factor_recargo' => 1.25,
//     'valor_hora_extra' => 6250,
//     'costo_total' => 12500,  // $12,500 COP (1.25x)
//     'es_diurna' => true,
//     'es_dominical_festivo' => false
// ]
```

### Ejemplo 4: Calcular costo de horas extras nocturnas

```php
// Martes - hora extra nocturna (22:00-00:00)
$resultado = $Costos->calcularCostoHorasExtras(
    $usuario_id = 1,
    $horas = 2.0,
    $hora_inicio = '22:00',
    $hora_fin = '00:00',
    $fecha = '2026-01-07'  // Martes
);

// Resultado:
// [
//     'tipo_recargo' => 'extra_nocturna',
//     'factor_recargo' => 1.75,
//     'costo_total' => 17500,  // $17,500 COP (1.75x)
//     'es_diurna' => false
// ]
```

### Ejemplo 5: Calcular costo de horas extras dominicales

```php
// Domingo - hora extra nocturna (22:00-00:00)
$resultado = $Costos->calcularCostoHorasExtras(
    $usuario_id = 1,
    $horas = 2.0,
    $hora_inicio = '22:00',
    $hora_fin = '00:00',
    $fecha = '2026-01-11'  // Domingo
);

// Resultado:
// [
//     'tipo_recargo' => 'dominical_nocturno',
//     'factor_recargo' => 2.5,
//     'costo_total' => 25000,  // $25,000 COP (2.5x)
//     'es_dominical_festivo' => true
// ]
```

## 🔍 Detección Automática

### Verificar si un día es festivo

```php
$Costos = new Costos();

// Verificar Año Nuevo
$es_festivo = $Costos->esFestivo('2026-01-01');  // true

// Verificar día ordinario
$es_festivo = $Costos->esFestivo('2026-01-06');  // false
```

### Verificar si es domingo

```php
$es_domingo = $Costos->esDomingo('2026-01-11');  // true (domingo)
```

### Verificar si es dominical o festivo

```php
// Detecta AMBOS: domingos Y festivos
$es_especial = $Costos->esDominicalOFestivo('2026-01-11');  // true (domingo)
$es_especial = $Costos->esDominicalOFestivo('2026-01-01');  // true (festivo)
```

### Verificar horario diurno/nocturno

```php
$es_diurna = $Costos->esDiurna('14:00');  // true (dentro de 06:00-21:00)
$es_diurna = $Costos->esDiurna('22:00');  // false (fuera del horario)
```

## 📊 Integración con Sincronización

El sistema de sincronización con ProjectDashboard ahora usa automáticamente estos cálculos:

### [sincronizar-projectdashboard.php](sincronizar-projectdashboard.php)

```php
// El sistema calcula automáticamente:
// - Detecta festivos desde festivos_cache
// - Aplica factores según horario diurno/nocturno
// - Considera si es domingo o festivo
// - Usa factores correctos de legislación colombiana

$registros = $Sincronizacion->obtenerRegistrosPendientesAgrupados($filtros);

// Cada registro ya incluye:
// - tiempo_ordinario: horas regulares
// - tiempo_extra: horas extras
// - total_pagado: costo calculado con TODOS los recargos aplicados
```

### Flujo de Cálculo

1. **Obtener registros** → Consulta DB (horas normales + extras)
2. **Detectar contexto** → Fecha → ¿festivo? ¿domingo? ¿nocturno?
3. **Aplicar factor** → Según legislación colombiana
4. **Calcular costo** → `valor_hora_base × factor × horas`
5. **Sincronizar** → Enviar a ProjectDashboard con costo correcto

## 🧪 Script de Prueba

Ejecutar pruebas:

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/time-production
php test-calculos-costos.php
```

Esto probará:
- ✅ Horas ordinarias (lun-sáb)
- ✅ Horas dominicales
- ✅ Horas festivas
- ✅ Extras diurnas ordinarias
- ✅ Extras nocturnas ordinarias
- ✅ Extras dominicales diurnas
- ✅ Extras dominicales nocturnas
- ✅ Detección de festivos
- ✅ Detección de horarios

## 📝 Caso Real Completo

### Escenario: Trabajador en semana con domingo

**Datos:**
- Usuario: Juan Pérez (ID: 15)
- Valor hora base: $8,000 COP
- Semana: 6-12 enero 2026

**Registros:**

| Día | Fecha | Tipo | Horas | Horario | Factor | Costo |
|-----|-------|------|-------|---------|--------|-------|
| Lunes | 2026-01-06 | Regular | 8.0 | 08:00-17:00 | 1.0x | $64,000 |
| Martes | 2026-01-07 | Regular | 8.0 | 08:00-17:00 | 1.0x | $64,000 |
| Martes | 2026-01-07 | Extra nocturna | 2.0 | 22:00-00:00 | 1.75x | $28,000 |
| Miércoles | 2026-01-08 | Regular | 8.0 | 08:00-17:00 | 1.0x | $64,000 |
| Jueves | 2026-01-09 | Regular | 8.0 | 08:00-17:00 | 1.0x | $64,000 |
| Viernes | 2026-01-10 | Regular | 8.0 | 08:00-17:00 | 1.0x | $64,000 |
| Sábado | 2026-01-11 | Regular | 4.0 | 08:00-12:00 | 1.0x | $32,000 |
| **Domingo** | **2026-01-11** | **Dominical** | **6.0** | **10:00-16:00** | **1.75x** | **$84,000** |

**Total semanal:**
- Horas ordinarias: 44.0 → $380,000 COP
- Horas extras nocturnas: 2.0 → $28,000 COP
- Horas dominicales: 6.0 → $84,000 COP
- **TOTAL: $492,000 COP**

## 🔐 Seguridad

- ✅ Validación de fechas en formato `Y-m-d`
- ✅ Valores numéricos sanitizados con `floatval()`
- ✅ Configuración cacheada para performance
- ✅ Manejo de errores con try-catch
- ✅ Consulta festivos con fallback

## 📚 Referencias

- **Código del Trabajo Colombia**: Artículos 159-167 (jornada laboral)
- **Decreto 2351/1965**: Horas extras y recargos
- **Ley 50/1990**: Trabajo dominical y festivo
- **API Festivos**: [Nager.Date](https://date.nager.at/)

## 🆘 Solución de Problemas

### Problema: Festivos no se detectan

**Solución:**
```bash
# Consultar festivos manualmente
navegador -> Configuración -> Días Festivos -> Seleccionar año -> Consultar

# O via cron
php cron-festivos.php
```

### Problema: Factores incorrectos

**Solución:**
```sql
-- Verificar configuración
SELECT clave, valor FROM configuracion_sistema WHERE categoria = 'costos';

-- Actualizar si es necesario (desde Configuración -> Costos y Tarifas)
```

### Problema: Horario diurno/nocturno incorrecto

**Solución:**
```sql
-- Verificar horarios
SELECT clave, valor FROM configuracion_sistema 
WHERE clave IN ('hora_diurna_inicio', 'hora_diurna_fin');

-- Por defecto: 06:00 - 21:00 (configurar en UI si necesario)
```

---

**Última actualización**: 7 enero 2026  
**Versión**: 2.0  
**Legislación**: Colombia  
