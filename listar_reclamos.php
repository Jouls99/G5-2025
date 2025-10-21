


<?php
header('Content-Type: application/json');
include 'db.php';

$filtro = $_GET['q'] ?? '';

$sql = "
    SELECT 
        r.numero_ticket,
        r.descripcion,
        r.respuestas,
        r.estado,
        r.prioridad,
        r.creado_en,
        c.nombre as cliente_nombre,
        c.apellido as cliente_apellido,
        c.dni as cliente_dni,
        l.nombre as localidad_nombre,
        a.nombre_completo as asignado_a_nombre
    FROM reclamos r
    JOIN clientes c ON r.cliente_id = c.id
    JOIN localidades l ON c.localidad_id = l.id
    LEFT JOIN administradores a ON r.asignado_a = a.id
";

if ($filtro) {
    $sql .= " WHERE r.numero_ticket LIKE ? OR c.dni LIKE ? ORDER BY r.creado_en DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$filtro%", "%$filtro%"]);
} else {
    $sql .= " ORDER BY r.creado_en DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}

$reclamos = $stmt->fetchAll();

foreach ($reclamos as &$r) {
    $r['respuestas'] = json_decode($r['respuestas'], true);
}
unset($r);

echo json_encode($reclamos);
?>