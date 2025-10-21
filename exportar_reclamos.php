<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('default_charset', 'UTF-8');

// Incluir conexión
include 'db.php';

// Consultar todos los reclamos (ordenados por fecha descendente)
try {
    $stmt = $pdo->query("
    SELECT 
        r.numero_ticket,
        c.nombre AS cliente_nombre,
        c.apellido AS cliente_apellido,
        c.dni AS cliente_dni,
        l.nombre AS localidad_nombre,
        r.descripcion,
        r.respuestas,
        r.estado,
        r.creado_en AS fecha_registro
    FROM reclamos r
    INNER JOIN clientes c ON r.cliente_id = c.id
    LEFT JOIN localidades l ON c.localidad_id = l.id
    ORDER BY r.creado_en DESC
");
    $reclamos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener reclamos: " . $e->getMessage());
}

// Formato de fecha para el nombre del archivo: YYYYMMDD_HHMM
$fechaArchivo = date('Ymd_His');

// Configurar encabezados para descarga
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="RECLAMOS_FIONET_' . $fechaArchivo . '.csv"');

// Asegurar que Excel lea UTF-8 correctamente
echo "\xEF\xBB\xBF"; // BOM para UTF-8 en Excel

// Abrir flujo de salida
$output = fopen('php://output', 'w');

// Escribir encabezados en español claro
fputcsv($output, [
    'N° Reclamo',
    'Nombre',
    'Apellido',
    'DNI',
    'Localidad',
    'Descripción del Problema',
    'Preguntas Técnicas (Respuestas)',
    'Estado Actual',
    'Fecha de Registro'
]);

// Escribir datos
foreach ($reclamos as $r) {
    // Decodificar preguntas técnicas (JSON)
    $respuestas = isset($r['respuestas']) ? json_decode($r['respuestas'], true) : [];
    $respuestasTxt = '';
    if (is_array($respuestas) && !empty($respuestas)) {
        $respuestasTxt = implode('; ', array_map(
            function($pregunta, $respuesta) {
                // Formatear preguntas para que sean legibles
                $pregunta = str_replace(['router', 'cables', 'dispositivo', 'wifi', 'los', 'contrasena', 'error'], [
                    '¿Reinició el router?',
                    '¿Revisó los cables?',
                    '¿Probó otro dispositivo?',
                    '¿Ve su red WiFi?',
                    '¿La luz LOS está encendida?',
                    '¿Compartió su contraseña?',
                    '¿Le aparece algún mensaje de error?'
                ], $pregunta);
                return "$pregunta: $respuesta";
            },
            array_keys($respuestas),
            $respuestas
        ));
    }

    // Formatear fecha a un formato legible: dd/mm/yyyy hh:mm
    $fechaLegible = '';
    if (!empty($r['fecha_registro'])) {
        $fechaLegible = date('d/m/Y H:i', strtotime($r['fecha_registro']));
    }

    fputcsv($output, [
        $r['numero_ticket'] ?? '',
        $r['cliente_nombre'] ?? '',
        $r['cliente_apellido'] ?? '',
        $r['cliente_dni'] ?? '',
        $r['localidad_nombre'] ?? '',
        $r['descripcion'] ?? '',
        $respuestasTxt,
        $r['estado'] ?? '',
        $fechaLegible
    ]);
}

fclose($output);
exit;
?>