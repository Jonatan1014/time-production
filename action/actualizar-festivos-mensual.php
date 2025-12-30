<?php
// action/actualizar-festivos-mensual.php
// Script para actualizar festivos mensualmente
// Se puede ejecutar vía cron: 0 0 1 * * /usr/bin/php /path/to/this/file.php

require_once '../includes/Class-festivos.php';

$festivos = new Festivos();
$resultado = $festivos->actualizarFestivosMensual();

if ($resultado) {
    echo "Festivos actualizados correctamente.\n";
    exit(0);
} else {
    echo "Error al actualizar festivos.\n";
    exit(1);
}
?>