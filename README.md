# 📊 Time Production - Sistema de Gestión de Horas

## 🏢 Talleres Unidos Ltda.

Sistema integral para el registro, control y sincronización de horas de trabajo normales y extras. Permite la gestión completa del tiempo trabajado por empleados en órdenes de producción, con cálculo automático de costos y sincronización con sistemas externos.

---

## 📑 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Roles de Usuario](#roles-de-usuario)
4. [Gestión de Usuarios](#gestión-de-usuarios)
5. [Departamentos y Cargos](#departamentos-y-cargos)
6. [Órdenes de Producción](#órdenes-de-producción)
7. [Configuración de Horarios Laborales](#configuración-de-horarios-laborales)
8. [Registro de Horas Normales](#registro-de-horas-normales)
9. [Gestión de Horas Extras](#gestión-de-horas-extras)
10. [Cálculo de Costos](#cálculo-de-costos)
11. [Sincronización con Project Dashboard](#sincronización-con-project-dashboard)
12. [Configuración del Webhook n8n](#configuración-del-webhook-n8n)
13. [Flujo de Datos Completo](#flujo-de-datos-completo)
14. [Reportes y Exportación](#reportes-y-exportación)

---

## 📋 Descripción General

**Time Production** es un sistema web desarrollado en PHP que permite:

- ✅ Gestionar usuarios con diferentes roles y permisos
- ✅ Registrar horas de trabajo normales por orden de producción
- ✅ Solicitar y aprobar horas extras
- ✅ Calcular costos automáticamente según tarifas configurables
- ✅ Sincronizar información con sistemas externos vía webhook
- ✅ Generar reportes de costos por usuario y proyecto

---

## 🏗️ Arquitectura del Sistema

```
┌─────────────────────────────────────────────────────────────────┐
│                    TIME PRODUCTION SYSTEM                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│   ┌──────────────┐    ┌──────────────┐    ┌──────────────┐     │
│   │   Frontend   │───▶│   Backend    │───▶│   Database   │     │
│   │   (HTML/JS)  │◀───│   (PHP)      │◀───│   (MySQL)    │     │
│   └──────────────┘    └──────────────┘    └──────────────┘     │
│                              │                                   │
│                              ▼                                   │
│                    ┌──────────────────┐                         │
│                    │    Webhook       │                         │
│                    │    (n8n)         │                         │
│                    └────────┬─────────┘                         │
│                              │                                   │
│                              ▼                                   │
│                    ┌──────────────────┐                         │
│                    │   Google Sheets  │                         │
│                    │  (Project Dash)  │                         │
│                    └──────────────────┘                         │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### Estructura de Carpetas

```
time-production/
├── action/                    # Controladores de acciones (AJAX/POST)
│   ├── add-usuarios.php       # Crear usuarios
│   ├── add-registro-horas.php # Registrar horas
│   ├── aprobar-hora-extra.php # Aprobar horas extras
│   ├── enviar-webhook.php     # Enviar datos a n8n
│   └── ...
├── assets/                    # Recursos estáticos
│   ├── css/                   # Estilos
│   ├── js/                    # JavaScript
│   └── images/                # Imágenes
├── includes/                  # Clases PHP y componentes
│   ├── Class-usuario.php      # Gestión de usuarios
│   ├── Class-registro-horas.php
│   ├── Class-horas-extras.php
│   ├── Class-sincronizacion.php
│   ├── Class-webhook.php
│   ├── Class-costos.php
│   └── conn-db.php            # Conexión a base de datos
├── configuracion.php          # Panel de configuración
├── usuarios.php               # Gestión de usuarios
├── registrar-horas.php        # Registro de horas
├── horas-extras.php           # Gestión de horas extras
└── sincronizar-projectdashboard.php
```

---

## 👥 Roles de Usuario

El sistema maneja diferentes niveles de acceso:

### 🔴 Administrador
| Permiso | Descripción |
|---------|-------------|
| ✅ Gestión completa de usuarios | Crear, editar, activar/desactivar usuarios |
| ✅ Configuración del sistema | Horarios, costos, integraciones |
| ✅ Aprobar/Rechazar horas extras | Flujo de aprobación |
| ✅ Validar registros de horas | Confirmación de horas normales |
| ✅ Sincronización con externos | Envío a Project Dashboard |
| ✅ Reportes completos | Acceso a todos los reportes |

### 🟢 Trabajador
| Permiso | Descripción |
|---------|-------------|
| ✅ Registrar horas normales | Asociadas a órdenes de producción |
| ✅ Solicitar horas extras | Requiere aprobación |
| ✅ Ver sus propias horas | Historial personal |
| ✅ Editar perfil | Datos personales |
| ❌ Sin acceso a configuración | Solo funciones básicas |

---

## 👤 Gestión de Usuarios

### Crear un Nuevo Usuario

**Ruta:** `Usuarios → Agregar Usuario` o `/add-usuarios.php`

```
┌──────────────────────────────────────────────────────────────┐
│                    FORMULARIO DE USUARIO                      │
├──────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌─────────────────────┐  ┌─────────────────────┐           │
│  │ Nombre Completo *   │  │ Username *          │           │
│  │ [________________]  │  │ [________________]  │           │
│  └─────────────────────┘  └─────────────────────┘           │
│                                                               │
│  ┌─────────────────────┐  ┌─────────────────────┐           │
│  │ Email *             │  │ Contraseña *        │           │
│  │ [________________]  │  │ [________________]  │           │
│  └─────────────────────┘  └─────────────────────┘           │
│                                                               │
│  ┌─────────────────────┐  ┌─────────────────────┐           │
│  │ Rol *               │  │ Departamento        │           │
│  │ [▼ Seleccionar   ]  │  │ [▼ Seleccionar   ]  │           │
│  └─────────────────────┘  └─────────────────────┘           │
│                                                               │
│  ┌─────────────────────┐  ┌─────────────────────┐           │
│  │ Cargo               │  │ Valor Hora Base *   │           │
│  │ [▼ Seleccionar   ]  │  │ [$ 7.500        ]   │           │
│  └─────────────────────┘  └─────────────────────┘           │
│                                                               │
│  ┌─────────────────────┐                                     │
│  │ Fecha de Ingreso    │                                     │
│  │ [dd/mm/aaaa      ]  │                                     │
│  └─────────────────────┘                                     │
│                                                               │
│              [  Guardar Usuario  ]                           │
│                                                               │
└──────────────────────────────────────────────────────────────┘
```

### Campos Importantes

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `nombre_completo` | String | Nombre y apellidos del empleado |
| `username` | String | Usuario único para login |
| `email` | String | Correo electrónico (único) |
| `password` | String | Encriptada con `password_hash()` |
| `rol` | Enum | `administrador` / `trabajador` |
| `departamento_id` | FK | Área de trabajo |
| `cargo_id` | FK | Puesto del empleado |
| `valor_hora_base` | Decimal | **💰 Costo por hora normal** |
| `fecha_ingreso` | Date | Fecha de incorporación |
| `is_active` | Boolean | Estado activo/inactivo |

### Valor Hora Base

> ⚠️ **IMPORTANTE:** El `valor_hora_base` es fundamental para el cálculo de costos. Este valor se usa como base para:
> - Cálculo de horas normales: `horas × valor_hora_base`
> - Cálculo de horas extras: `horas × valor_hora_base × (1 + porcentaje_recargo)`

---

## 🏛️ Departamentos y Cargos

### Departamentos

**Ruta:** `Configuración → Departamentos` o `/departamentos.php`

Los departamentos organizan a los empleados por áreas de trabajo:

```
┌────────────────────────────────────────────┐
│              DEPARTAMENTOS                  │
├────────────────────────────────────────────┤
│ ID │ Nombre        │ Código │ Responsable  │
├────────────────────────────────────────────┤
│ 1  │ Producción    │ PROD   │ Juan Pérez   │
│ 2  │ Mantenimiento │ MANT   │ Pedro García │
│ 3  │ Calidad       │ CAL    │ María López  │
│ 4  │ Logística     │ LOG    │ Ana Torres   │
└────────────────────────────────────────────┘
```

### Cargos

**Ruta:** `Configuración → Cargos` o `/cargos.php`

Los cargos definen los puestos de trabajo:

```
┌─────────────────────────────────────────┐
│               CARGOS                     │
├─────────────────────────────────────────┤
│ ID │ Nombre            │ Estado         │
├─────────────────────────────────────────┤
│ 1  │ Operador          │ 🟢 Activo      │
│ 2  │ Supervisor        │ 🟢 Activo      │
│ 3  │ Técnico           │ 🟢 Activo      │
│ 4  │ Soldador          │ 🟢 Activo      │
│ 5  │ Mecánico          │ 🔴 Inactivo    │
└─────────────────────────────────────────┘
```

---

## 📦 Órdenes de Producción

**Ruta:** `Órdenes de Producción` o `/ordenes-produccion.php`

Las órdenes de producción son el centro del registro de horas. Cada hora trabajada debe estar asociada a una OP.

### Estructura de una Orden

```
┌──────────────────────────────────────────────────────────────┐
│              ORDEN DE PRODUCCIÓN                              │
├──────────────────────────────────────────────────────────────┤
│                                                               │
│  Código OP: OP-2024-0156                                     │
│  ─────────────────────────────                               │
│                                                               │
│  📦 Producto: Estructura Metálica Tipo A                     │
│  👤 Cliente: Construcciones ABC                              │
│  📝 Descripción: Fabricación de estructura para nave         │
│                                                               │
│  ┌───────────────────┬───────────────────┐                   │
│  │ Fecha Inicio      │ Fecha Fin Estimada│                   │
│  │ 15/01/2024        │ 28/02/2024        │                   │
│  └───────────────────┴───────────────────┘                   │
│                                                               │
│  Cantidad Objetivo: 50 unidades                              │
│                                                               │
│  Estado: 🟢 Activa    Prioridad: 🔶 Alta                    │
│                                                               │
│  ┌──────────────────────────────────────────┐                │
│  │ ESTADÍSTICAS                             │                │
│  │ • Total Registros: 45                    │                │
│  │ • Usuarios Involucrados: 8               │                │
│  │ • Horas Trabajadas: 156.5 hrs            │                │
│  └──────────────────────────────────────────┘                │
│                                                               │
└──────────────────────────────────────────────────────────────┘
```

### Estados de Orden

| Estado | Descripción |
|--------|-------------|
| 🟢 Activa | En producción, acepta registros de horas |
| 🟡 Pausada | Temporalmente detenida |
| ✅ Completada | Finalizada |
| ❌ Cancelada | No se continúa |

### Prioridades

| Prioridad | Color | Orden de Vista |
|-----------|-------|----------------|
| 🔴 Urgente | Rojo | 1° (primero) |
| 🔶 Alta | Naranja | 2° |
| 🟡 Media | Amarillo | 3° |
| 🟢 Baja | Verde | 4° (último) |

---

## ⏰ Configuración de Horarios Laborales

**Ruta:** `Configuración → Horarios Laborales` o pestaña en `/configuracion.php`

### Horarios por Día de la Semana

El sistema permite configurar los horarios de trabajo para cada día:

```
┌────────────────────────────────────────────────────────────────────────────┐
│                         HORARIOS LABORALES                                  │
├────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  DÍA        │ LABORABLE │ MAÑANA        │ TARDE         │ HORAS TOTALES   │
│  ───────────┼───────────┼───────────────┼───────────────┼─────────────────│
│  Lunes      │    ✅     │ 08:00 - 12:00 │ 14:00 - 18:00 │      8.0        │
│  Martes     │    ✅     │ 08:00 - 12:00 │ 14:00 - 18:00 │      8.0        │
│  Miércoles  │    ✅     │ 08:00 - 12:00 │ 14:00 - 18:00 │      8.0        │
│  Jueves     │    ✅     │ 08:00 - 12:00 │ 14:00 - 18:00 │      8.0        │
│  Viernes    │    ✅     │ 08:00 - 12:00 │ 14:00 - 17:00 │      7.0        │
│  Sábado     │    ⬜     │ 00:00 - 00:00 │ 00:00 - 00:00 │      0.0        │
│  Domingo    │    ⬜     │ 00:00 - 00:00 │ 00:00 - 00:00 │      0.0        │
│                                                                             │
└────────────────────────────────────────────────────────────────────────────┘
```

### Campos de Horario

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `dia_semana` | String | lunes, martes, ..., domingo |
| `es_laborable` | Boolean | Define si se trabaja ese día |
| `hora_inicio_manana` | Time | Inicio jornada mañana |
| `hora_fin_manana` | Time | Fin jornada mañana |
| `hora_inicio_tarde` | Time | Inicio jornada tarde |
| `hora_fin_tarde` | Time | Fin jornada tarde |
| `horas_totales` | Decimal | Total de horas del día |

### Uso en Validaciones

Los horarios se usan para:
1. ✅ Validar si una fecha es laborable
2. ✅ Calcular horas máximas permitidas por día
3. ✅ Determinar si se requieren horas extras

---

## 📝 Registro de Horas Normales

**Ruta:** `Registrar Horas` o `/registrar-horas.php`

### Proceso de Registro

```
┌──────────────────────────────────────────────────────────────┐
│              REGISTRAR HORAS TRABAJADAS                       │
├──────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌─────────────────────────────────────────┐                 │
│  │ Orden de Producción *                   │                 │
│  │ [▼ OP-2024-0156 - Estructura Metálica ] │                 │
│  └─────────────────────────────────────────┘                 │
│                                                               │
│  ┌──────────────────┐  ┌──────────────────┐                  │
│  │ Fecha *          │  │ Horas Trabajadas*│                  │
│  │ [27/01/2024   ]  │  │ [▼ 8.0 horas   ] │                  │
│  └──────────────────┘  └──────────────────┘                  │
│                                                               │
│  ┌─────────────────────────────────────────┐                 │
│  │ Descripción del Trabajo *               │                 │
│  │ ┌─────────────────────────────────────┐ │                 │
│  │ │ Soldadura de vigas principales y   │ │                 │
│  │ │ preparación de soportes laterales  │ │                 │
│  │ └─────────────────────────────────────┘ │                 │
│  └─────────────────────────────────────────┘                 │
│                                                               │
│  ┌─────────────────────────────────────────┐                 │
│  │ Observaciones (opcional)                │                 │
│  │ ┌─────────────────────────────────────┐ │                 │
│  │ │                                     │ │                 │
│  │ └─────────────────────────────────────┘ │                 │
│  └─────────────────────────────────────────┘                 │
│                                                               │
│              [  Registrar Horas  ]                           │
│                                                               │
└──────────────────────────────────────────────────────────────┘
```

### Límites y Validaciones

| Parámetro | Valor | Constante |
|-----------|-------|-----------|
| Horas mínimas por registro | 0.5 hrs | `HORAS_MIN_NORMALES` |
| Horas máximas por día | 8.5 hrs | `HORAS_MAX_NORMALES` |
| Incremento de horas | 0.5 hrs | Configurable |

### Estados del Registro

```
┌─────────────────────────────────────────────────────────────┐
│                    FLUJO DE ESTADOS                          │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│   ┌────────────┐     ┌────────────┐     ┌────────────┐     │
│   │            │     │            │     │            │     │
│   │ REGISTRADO │────▶│  VALIDADO  │     │ RECHAZADO  │     │
│   │            │     │            │     │            │     │
│   └────────────┘     └────────────┘     └────────────┘     │
│        │                   │                   ▲            │
│        │                   │                   │            │
│        └───────────────────┼───────────────────┘            │
│                            │                                 │
│                            ▼                                 │
│                   ┌────────────────┐                        │
│                   │ SINCRONIZADO   │                        │
│                   │ (Enviado a     │                        │
│                   │  Project Dash) │                        │
│                   └────────────────┘                        │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Reglas de Negocio

1. ❌ **No duplicados:** No se puede registrar la misma OP + fecha dos veces
2. ❌ **Límite diario:** La suma de horas del día no puede exceder 8.5 hrs
3. ❌ **Edición bloqueada:** No se pueden editar registros ya validados
4. ❌ **Eliminación bloqueada:** No se pueden eliminar registros validados

---

## ⏱️ Gestión de Horas Extras

**Ruta Trabajador:** `Solicitar Horas Extras` o `/solicitar-horas-extras.php`  
**Ruta Admin:** `Horas Extras` o `/horas-extras.php`

### Flujo de Solicitud y Aprobación

```
┌─────────────────────────────────────────────────────────────────────────┐
│                      FLUJO DE HORAS EXTRAS                               │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  TRABAJADOR                            ADMINISTRADOR                     │
│  ══════════                            ══════════════                    │
│                                                                          │
│  ┌───────────────────┐                                                  │
│  │ 1. SOLICITAR      │                                                  │
│  │    Horas Extras   │──────┐                                           │
│  │    • Orden Prod.  │      │                                           │
│  │    • Fecha        │      │                                           │
│  │    • Hora inicio  │      │                                           │
│  │    • Hora fin     │      ▼                                           │
│  │    • Descripción  │  ┌────────────────┐                              │
│  └───────────────────┘  │  PENDIENTE     │                              │
│                          │  (En espera)   │                              │
│                          └───────┬────────┘                              │
│                                  │                                       │
│                          ┌───────┴────────┐                              │
│                          ▼                ▼                              │
│                  ┌────────────┐    ┌────────────┐                       │
│                  │  APROBADA  │    │  RECHAZADA │                       │
│                  │     ✅     │    │     ❌     │                       │
│                  └─────┬──────┘    └────────────┘                       │
│                        │                                                 │
│                        ▼                                                 │
│                  ┌────────────────────┐                                 │
│                  │ DISPONIBLE PARA    │                                 │
│                  │ SINCRONIZACIÓN     │                                 │
│                  └────────────────────┘                                 │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

### Formulario de Solicitud

```
┌──────────────────────────────────────────────────────────────┐
│            SOLICITAR HORAS EXTRAS                             │
├──────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌─────────────────────────────────────────┐                 │
│  │ Orden de Producción *                   │                 │
│  │ [▼ OP-2024-0156 - Estructura Metálica ] │                 │
│  └─────────────────────────────────────────┘                 │
│                                                               │
│  ┌──────────────────┐                                        │
│  │ Fecha *          │                                        │
│  │ [27/01/2024   ]  │                                        │
│  └──────────────────┘                                        │
│                                                               │
│  ┌──────────────────┐  ┌──────────────────┐                  │
│  │ Hora Inicio *    │  │ Hora Fin *       │                  │
│  │ [18:00        ]  │  │ [21:00        ]  │                  │
│  └──────────────────┘  └──────────────────┘                  │
│                                                               │
│  Total horas extras calculadas: 3.0 horas                    │
│                                                               │
│  ┌─────────────────────────────────────────┐                 │
│  │ Descripción del Trabajo *               │                 │
│  │ ┌─────────────────────────────────────┐ │                 │
│  │ │ Finalización urgente de soldadura  │ │                 │
│  │ │ para entrega del cliente           │ │                 │
│  │ └─────────────────────────────────────┘ │                 │
│  └─────────────────────────────────────────┘                 │
│                                                               │
│              [  Enviar Solicitud  ]                          │
│                                                               │
└──────────────────────────────────────────────────────────────┘
```

### Límites de Horas Extras

| Parámetro | Valor | Constante |
|-----------|-------|-----------|
| Horas mínimas por solicitud | 0.5 hrs | `HORAS_MIN_EXTRAS` |
| Horas máximas por solicitud | 8.0 hrs | `HORAS_MAX_EXTRAS` |
| Máximo total por día (normal + extra) | 16.0 hrs | `HORAS_MAX_DIA` |

### Cálculo Automático de Horas

El sistema calcula automáticamente el total de horas extras basándose en:
- `hora_inicio` y `hora_fin`
- Maneja cruces de medianoche automáticamente

```php
// Ejemplo: 22:00 a 02:00 del día siguiente
$hora_inicio = "22:00";
$hora_fin = "02:00";
// Resultado: 4 horas extras
```

---

## 💰 Cálculo de Costos

**Ruta:** `Configuración → Costos y Tarifas`

### Configuración de Turnos

El sistema diferencia entre horas diurnas y nocturnas:

```
┌──────────────────────────────────────────────────────────────┐
│               CONFIGURACIÓN DE TURNOS                         │
├──────────────────────────────────────────────────────────────┤
│                                                               │
│           TURNO DIURNO                TURNO NOCTURNO         │
│        ════════════════            ═══════════════════       │
│                                                               │
│        ┌────────────────┐          ┌────────────────┐        │
│        │  06:00 - 18:00 │          │  18:00 - 06:00 │        │
│        │                │          │  (siguiente)   │        │
│        │   ☀️ Día       │          │    🌙 Noche    │        │
│        └────────────────┘          └────────────────┘        │
│                                                               │
└──────────────────────────────────────────────────────────────┘
```

### Porcentajes de Recargo

| Tipo | Recargo | Multiplicador |
|------|---------|---------------|
| Hora Extra Diurna | 25% | × 1.25 |
| Hora Extra Nocturna | 75% | × 1.75 |

### Fórmulas de Cálculo

#### Horas Normales
```
Costo = horas_trabajadas × valor_hora_base
```

**Ejemplo:**
```
Juan tiene valor_hora_base = $7,500
Trabajó 8 horas normales

Costo = 8 × $7,500 = $60,000
```

#### Horas Extras Diurnas
```
Costo = horas_extras × valor_hora_base × (1 + porcentaje_recargo/100)
Costo = horas_extras × valor_hora_base × 1.25
```

**Ejemplo:**
```
Juan tiene valor_hora_base = $7,500
Trabajó 3 horas extras diurnas (de 18:00 a 21:00... pero iniciando antes de las 18:00)

Costo = 3 × $7,500 × 1.25 = $28,125
```

#### Horas Extras Nocturnas
```
Costo = horas_extras × valor_hora_base × (1 + porcentaje_recargo/100)
Costo = horas_extras × valor_hora_base × 1.75
```

**Ejemplo:**
```
Juan tiene valor_hora_base = $7,500
Trabajó 2 horas extras nocturnas (de 20:00 a 22:00)

Costo = 2 × $7,500 × 1.75 = $26,250
```

### Tabla Resumen de Costos

```
┌────────────────────────────────────────────────────────────────────┐
│                    EJEMPLO DE CÁLCULO DIARIO                       │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│  Empleado: Juan Pérez                                              │
│  Valor Hora Base: $7,500                                           │
│  Fecha: 27/01/2024                                                 │
│                                                                    │
│  ┌────────────────┬────────────┬───────────────┬──────────────┐   │
│  │ Tipo           │ Horas      │ Tarifa        │ Subtotal     │   │
│  ├────────────────┼────────────┼───────────────┼──────────────┤   │
│  │ Normales       │ 8.0        │ $7,500        │ $60,000      │   │
│  │ Extra Diurna   │ 1.5        │ $9,375 (+25%) │ $14,063      │   │
│  │ Extra Nocturna │ 2.0        │ $13,125 (+75%)│ $26,250      │   │
│  ├────────────────┼────────────┼───────────────┼──────────────┤   │
│  │ TOTAL          │ 11.5       │               │ $100,313     │   │
│  └────────────────┴────────────┴───────────────┴──────────────┘   │
│                                                                    │
└────────────────────────────────────────────────────────────────────┘
```

---

## 🔄 Sincronización con Project Dashboard

**Ruta:** `Sincronizar Project Dashboard` o `/sincronizar-projectdashboard.php`

### Visión General

El sistema permite sincronizar los registros de horas con un sistema externo llamado **Project Dashboard** mediante un webhook.

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    FLUJO DE SINCRONIZACIÓN                              │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  TIME PRODUCTION                   n8n                   GOOGLE SHEETS  │
│  ═══════════════                ════════                ═══════════════ │
│                                                                         │
│  ┌─────────────────┐          ┌─────────────┐         ┌──────────────┐  │
│  │ Registros       │          │             │         │              │  │
│  │ Pendientes      │────▶     │  WEBHOOK    │────▶    │ Project      │  │
│  │ • Horas Normales│   POST   │  Receptor   │  API    │ Dashboard    │  │
│  │ • Horas Extras  │   JSON   │             │         │ (Sheet)      │  │
│  └─────────────────┘          └─────────────┘         └──────────────┘  │
│                                      │                        │         │
│                                      ▼                        │         │
│                              ┌─────────────┐                  │         │
│                              │  Procesar   │                  │         │
│                              │  y Ordenar  │                  │         │
│                              │  Datos      │                  │         │
│                              └─────────────┘                  │         │
│                                                               │         │
│  ┌─────────────────┐                                         │          │
│  │ Marcar como     │◀────────────────────────────────────────┘          │
│  │ Sincronizado    │          Confirmación                              │
│  └─────────────────┘                                                    │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

### Configuración de la Integración

**Ruta:** `Configuración → Integraciones`

```
┌──────────────────────────────────────────────────────────────┐
│           CONFIGURACIÓN PROJECT DASHBOARD                     │
├──────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌─ Habilitar Sincronización ──────────────────────────────┐ │
│  │ [✓] Activar integración con ProjectDashboard            │ │
│  └─────────────────────────────────────────────────────────┘ │
│                                                               │
│  ┌─ URL Webhook de ProjectDashboard ───────────────────────┐ │
│  │ https://n8n.ejemplo.com/webhook/abc123-proyecto         │ │
│  └─────────────────────────────────────────────────────────┘ │
│                                                               │
│  ┌─ Token de Autenticación ────────────────────────────────┐ │
│  │ ••••••••••••••••••••••••                                │ │
│  └─────────────────────────────────────────────────────────┘ │
│                                                               │
│  ┌─ Sincronización Automática ─────────────────────────────┐ │
│  │ [ ] Enviar automáticamente vía webhook al aprobar horas │ │
│  └─────────────────────────────────────────────────────────┘ │
│                                                               │
│  [Probar Conexión]    [Ver Panel de Sincronización]         │
│                                                               │
└──────────────────────────────────────────────────────────────┘
```

### Panel de Sincronización

```
┌──────────────────────────────────────────────────────────────────────────┐
│              SINCRONIZACIÓN PROJECT DASHBOARD                             │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                           │
│  ┌─ ESTADÍSTICAS ────────────────────────────────────────────────────┐  │
│  │                                                                    │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────────────┐  │  │
│  │  │ TOTAL    │  │ NORMALES │  │ EXTRAS   │  │ SINCRONIZADOS    │  │  │
│  │  │ PENDIENT │  │ PENDIENT │  │ PENDIENT │  │ TOTAL            │  │  │
│  │  │   145    │  │    98    │  │    47    │  │     2,340        │  │  │
│  │  └──────────┘  └──────────┘  └──────────┘  └──────────────────┘  │  │
│  │                                                                    │  │
│  └────────────────────────────────────────────────────────────────────┘  │
│                                                                           │
│  ┌─ FILTROS ─────────────────────────────────────────────────────────┐  │
│  │  Fecha Inicio: [01/01/2024]   Fecha Fin: [31/01/2024]            │  │
│  │  Usuario: [▼ Todos los usuarios                    ]              │  │
│  │                                                     [Filtrar]     │  │
│  └────────────────────────────────────────────────────────────────────┘  │
│                                                                           │
│  ┌─ REGISTROS PENDIENTES DE SINCRONIZACIÓN ──────────────────────────┐  │
│  │                                                                    │  │
│  │  [ ] │ Empleado      │ Fecha      │ OP       │ Normal │ Extra    │  │
│  │  ────┼───────────────┼────────────┼──────────┼────────┼──────────│  │
│  │  [✓] │ Juan Pérez    │ 27/01/2024 │ OP-0156  │ 8.0    │ 2.0      │  │
│  │  [✓] │ María López   │ 27/01/2024 │ OP-0156  │ 8.0    │ 0.0      │  │
│  │  [✓] │ Pedro García  │ 27/01/2024 │ OP-0189  │ 6.5    │ 3.0      │  │
│  │  [ ] │ Ana Torres    │ 26/01/2024 │ OP-0156  │ 8.0    │ 1.5      │  │
│  │                                                                    │  │
│  └────────────────────────────────────────────────────────────────────┘  │
│                                                                           │
│  [Seleccionar Todos]  [Sincronizar Seleccionados]  [Enviar vía Webhook] │
│                                                                           │
└──────────────────────────────────────────────────────────────────────────┘
```

### Datos Enviados al Webhook

El sistema envía un JSON estructurado con la siguiente información:

```json
{
  "registros": [
    {
      "marca_temporal": "27/01/2024 14:30:25",
      "proyecto_numero": "OP-2024-0156",
      "fecha": "27/01/2024",
      "nombre_empleado": "Juan Pérez",
      "cargo": "Operador",
      "area_trabajo": "Producción",
      "tiempo_ordinario": "8,0",
      "tiempo_extra": "2,0",
      "total_pagado": "85000",
      "metadata": {
        "usuario_id": 15,
        "orden_produccion_id": 45,
        "tipo": "horas_normales",
        "tiempo_ordinario_numerico": 8.0,
        "tiempo_extra_numerico": 2.0,
        "total_pagado_numerico": 85000,
        "detalles_normales_ids": [234],
        "detalles_extras_ids": [56]
      }
    }
  ],
  "sistema_origen": "TIME_PRODUCTION",
  "timestamp": "2024-01-27T14:30:25-05:00"
}
```

### Formato de Datos

| Campo | Formato | Ejemplo | Descripción |
|-------|---------|---------|-------------|
| `marca_temporal` | dd/mm/yyyy HH:ii:ss | 27/01/2024 14:30:25 | Fecha/hora del envío |
| `proyecto_numero` | String | OP-2024-0156 | Código de la orden |
| `fecha` | dd/mm/yyyy | 27/01/2024 | Fecha del registro |
| `nombre_empleado` | String | Juan Pérez | Nombre completo |
| `cargo` | String | Operador | Cargo del empleado |
| `area_trabajo` | String | Producción | Departamento |
| `tiempo_ordinario` | Decimal con coma | 8,0 | Horas normales |
| `tiempo_extra` | Decimal con coma | 2,0 | Horas extras |
| `total_pagado` | Número sin puntos | 85000 | Costo total (sin formato) |

---

## 🔗 Configuración del Webhook n8n

### Arquitectura n8n

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         FLUJO n8n                                        │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌────────────────┐    ┌────────────────┐    ┌────────────────┐        │
│  │                │    │                │    │                │        │
│  │   WEBHOOK      │───▶│   SET NODE     │───▶│   GOOGLE       │        │
│  │   Trigger      │    │   (Procesar)   │    │   SHEETS       │        │
│  │                │    │                │    │                │        │
│  └────────────────┘    └────────────────┘    └────────────────┘        │
│         │                     │                     │                   │
│         ▼                     ▼                     ▼                   │
│  Recibe JSON de      Extrae y formatea      Inserta fila en            │
│  Time Production     los datos              la hoja de cálculo          │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

### Paso 1: Crear Webhook en n8n

1. Crear nuevo workflow en n8n
2. Agregar nodo **Webhook**
3. Configurar:
   - **HTTP Method:** POST
   - **Path:** `/time-production-sync` (o cualquier nombre)
   - **Response Mode:** Last Node

```
URL resultante: https://tu-n8n.com/webhook/time-production-sync
```

### Paso 2: Procesar Datos (Set Node)

Agregar nodo **Set** para mapear campos:

```javascript
// Ejemplo de mapeo
{
  "Marca Temporal": {{ $json.registros[0].marca_temporal }},
  "Proyecto": {{ $json.registros[0].proyecto_numero }},
  "Fecha": {{ $json.registros[0].fecha }},
  "Empleado": {{ $json.registros[0].nombre_empleado }},
  "Cargo": {{ $json.registros[0].cargo }},
  "Área": {{ $json.registros[0].area_trabajo }},
  "Tiempo Ordinario": {{ $json.registros[0].tiempo_ordinario }},
  "Tiempo Extra": {{ $json.registros[0].tiempo_extra }},
  "Total Pagado": {{ $json.registros[0].total_pagado }}
}
```

### Paso 3: Conectar con Google Sheets

1. Agregar nodo **Google Sheets**
2. Configurar credenciales de Google
3. Configurar operación:
   - **Operation:** Append Row
   - **Document ID:** ID de tu hoja
   - **Sheet Name:** Nombre de la pestaña
4. Mapear columnas:

```
┌─────────────────────────────────────────────────────────────┐
│              MAPEO DE COLUMNAS GOOGLE SHEETS                 │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Columna Sheet          │   Campo Webhook                   │
│  ───────────────────────┼───────────────────────────────── │
│  A - Marca Temporal     │   marca_temporal                  │
│  B - Proyecto           │   proyecto_numero                 │
│  C - Fecha              │   fecha                           │
│  D - Empleado           │   nombre_empleado                 │
│  E - Cargo              │   cargo                           │
│  F - Área de Trabajo    │   area_trabajo                    │
│  G - Tiempo Ordinario   │   tiempo_ordinario                │
│  H - Tiempo Extra       │   tiempo_extra                    │
│  I - Total Pagado       │   total_pagado                    │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Paso 4: Manejo de Múltiples Registros

Para procesar múltiples registros en un solo envío:

```javascript
// Usar nodo SplitInBatches o Loop
// Para iterar sobre $json.registros[]
```

### Ejemplo de Workflow Completo

```
[Webhook] ──▶ [SplitInBatches] ──▶ [Set] ──▶ [Google Sheets] ──▶ [Respond]
    │              │                 │              │               │
    │              │                 │              │               │
 Recibir      Dividir en       Formatear       Insertar        Responder
 JSON         registros        campos          fila            OK/Error
```

---

## 🔀 Flujo de Datos Completo

### Diagrama de Flujo Completo

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        FLUJO COMPLETO DEL SISTEMA                            │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   SETUP INICIAL                                                             │
│   ═════════════                                                             │
│                                                                              │
│   ┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────────┐         │
│   │ Crear    │───▶│ Crear    │───▶│ Crear    │───▶│ Configurar   │         │
│   │ Depts    │    │ Cargos   │    │ Usuarios │    │ Horarios     │         │
│   └──────────┘    └──────────┘    └──────────┘    └──────────────┘         │
│                                         │                │                  │
│                                         ▼                ▼                  │
│                                   ┌──────────────────────────┐              │
│                                   │ Configurar Costos y      │              │
│                                   │ Tarifas de Recargo       │              │
│                                   └──────────────────────────┘              │
│                                                                              │
│   OPERACIÓN DIARIA                                                          │
│   ════════════════                                                          │
│                                                                              │
│   ┌──────────────┐                                                          │
│   │ Crear Orden  │                                                          │
│   │ de Producción│                                                          │
│   └──────┬───────┘                                                          │
│          │                                                                   │
│          ▼                                                                   │
│   ┌──────────────┐         ┌──────────────┐                                 │
│   │ Trabajador   │         │ Trabajador   │                                 │
│   │ Registra     │         │ Solicita     │                                 │
│   │ Horas        │         │ Horas Extras │                                 │
│   │ Normales     │         │              │                                 │
│   └──────┬───────┘         └──────┬───────┘                                 │
│          │                        │                                          │
│          │                        ▼                                          │
│          │                 ┌──────────────┐                                 │
│          │                 │ Admin        │                                 │
│          │                 │ Aprueba/     │                                 │
│          │                 │ Rechaza      │                                 │
│          │                 └──────┬───────┘                                 │
│          │                        │                                          │
│          ▼                        ▼                                          │
│   ┌──────────────────────────────────────┐                                  │
│   │      REGISTROS LISTOS PARA           │                                  │
│   │      SINCRONIZACIÓN                  │                                  │
│   │      • Horas Normales (registradas)  │                                  │
│   │      • Horas Extras (aprobadas)      │                                  │
│   └──────────────────┬───────────────────┘                                  │
│                      │                                                       │
│                      ▼                                                       │
│   ┌──────────────────────────────────────┐                                  │
│   │      CALCULAR COSTOS                 │                                  │
│   │      • Horas × Valor Base            │                                  │
│   │      • Extras × Valor × Recargo      │                                  │
│   └──────────────────┬───────────────────┘                                  │
│                      │                                                       │
│   SINCRONIZACIÓN     ▼                                                       │
│   ══════════════                                                            │
│                                                                              │
│   ┌──────────────────────────────────────┐                                  │
│   │      ADMIN SELECCIONA                │                                  │
│   │      REGISTROS A SINCRONIZAR         │                                  │
│   └──────────────────┬───────────────────┘                                  │
│                      │                                                       │
│                      ▼                                                       │
│   ┌──────────────────────────────────────┐                                  │
│   │      ENVIAR VÍA WEBHOOK              │                                  │
│   │      POST JSON ────────────────────▶ n8n                                │
│   └──────────────────┬───────────────────┘                                  │
│                      │                                                       │
│                      ▼                                                       │
│   ┌──────────────────────────────────────┐                                  │
│   │      n8n PROCESA Y ENVÍA             │                                  │
│   │      A GOOGLE SHEETS                 │                                  │
│   └──────────────────┬───────────────────┘                                  │
│                      │                                                       │
│                      ▼                                                       │
│   ┌──────────────────────────────────────┐                                  │
│   │      MARCAR COMO SINCRONIZADO        │                                  │
│   │      (No se puede reenviar)          │                                  │
│   └──────────────────────────────────────┘                                  │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 📊 Reportes y Exportación

**Ruta:** `Reportes` o `/reportes.php`

### Tipos de Reportes

#### 1. Reporte de Costos por Usuario
```
┌────────────────────────────────────────────────────────────────────┐
│                 REPORTE DE COSTOS POR USUARIO                       │
├────────────────────────────────────────────────────────────────────┤
│  Período: 01/01/2024 - 31/01/2024                                  │
│                                                                     │
│  ┌─────────────────┬────────┬────────┬──────────┬──────────────┐  │
│  │ Empleado        │ Normales│ Extras │ Total Hrs│ Costo Total  │  │
│  ├─────────────────┼────────┼────────┼──────────┼──────────────┤  │
│  │ Juan Pérez      │ 160.0  │ 24.5   │ 184.5    │ $1,650,000   │  │
│  │ María López     │ 152.0  │ 12.0   │ 164.0    │ $1,380,000   │  │
│  │ Pedro García    │ 168.0  │ 32.0   │ 200.0    │ $1,890,000   │  │
│  ├─────────────────┼────────┼────────┼──────────┼──────────────┤  │
│  │ TOTALES         │ 480.0  │ 68.5   │ 548.5    │ $4,920,000   │  │
│  └─────────────────┴────────┴────────┴──────────┴──────────────┘  │
│                                                                     │
│  [Exportar a Excel]                                                │
└────────────────────────────────────────────────────────────────────┘
```

#### 2. Reporte por Orden de Producción

Muestra horas y costos asociados a cada OP.

#### 3. Historial de Sincronizaciones

Registro de todos los envíos realizados al webhook.

### Exportación a Excel

El sistema permite exportar reportes en formato Excel (`.xlsx`) mediante la acción:
- `action/exportar-reporte-excel.php`

---

## ⚙️ Configuraciones Adicionales del Sistema

### Tabla de Configuraciones

| Clave | Tipo | Descripción | Valor Default |
|-------|------|-------------|---------------|
| `hora_diurna_inicio` | time | Inicio turno diurno | 06:00 |
| `hora_diurna_fin` | time | Fin turno diurno | 18:00 |
| `porcentaje_extra_diurna` | decimal | Recargo hora extra día | 25 |
| `porcentaje_extra_nocturna` | decimal | Recargo hora extra noche | 75 |
| `horas_maximas_por_dia` | decimal | Límite diario horas normales | 8.5 |
| `horas_maximas_extras` | decimal | Límite horas extras por solicitud | 8.0 |
| `incremento_horas` | decimal | Paso de incremento | 0.5 |
| `projectdashboard_habilitado` | boolean | Activar integración | false |
| `projectdashboard_url` | text | URL del webhook | - |
| `projectdashboard_webhook_token` | text | Token de autenticación | - |
| `projectdashboard_sincronizacion_automatica` | boolean | Envío automático | false |

---

## 🚀 Guía Rápida de Inicio

### 1. Configuración Inicial

```
1. Configurar conexión a base de datos
   └── includes/conn-db.php

2. Crear primer usuario Administrador
   └── Base de datos directamente o registro

3. Configurar Horarios Laborales
   └── Configuración → Horarios Laborales

4. Crear Departamentos
   └── Configuración → Departamentos

5. Crear Cargos
   └── Configuración → Cargos

6. Configurar Costos y Recargos
   └── Configuración → Costos y Tarifas

7. (Opcional) Configurar Webhook
   └── Configuración → Integraciones
```

### 2. Operación Diaria

```
1. Crear Órdenes de Producción (Admin)
   └── Órdenes de Producción → Nueva OP

2. Registrar Horas (Trabajadores)
   └── Registrar Horas → Seleccionar OP → Registrar

3. Solicitar Horas Extras (Trabajadores)
   └── Solicitar Horas Extras → Llenar formulario

4. Aprobar Horas Extras (Admin)
   └── Horas Extras → Revisar → Aprobar/Rechazar

5. Sincronizar (Admin)
   └── Sincronizar Project Dashboard → Seleccionar → Enviar
```

---

## 📞 Soporte

Para soporte técnico o consultas sobre el sistema, contactar al equipo de desarrollo.

---

## 📄 Licencia

Sistema propietario de **Talleres Unidos Ltda.**  
Todos los derechos reservados.

---

*Documentación actualizada: Enero 2026*
