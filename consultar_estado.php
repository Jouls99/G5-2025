<?php
header('Content-Type: application/json');
include 'db.php';

$consulta = $_GET['q'] ?? '';

if (!$consulta) {
    echo json_encode(['error' => 'Ingrese un número de ticket o DNI']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT r.numero_ticket, r.estado, c.dni
    FROM reclamos r
    JOIN clientes c ON r.cliente_id = c.id
    WHERE r.numero_ticket = ? OR c.dni = ?
    LIMIT 1
");
$stmt->execute([$consulta, $consulta]);
$reclamo = $stmt->fetch();

if ($reclamo) {
    echo json_encode([
        'success' => true,
        'numero_ticket' => $reclamo['numero_ticket'],
        'estado' => $reclamo['estado']
    ]);
} else {
    echo json_encode(['error' => '❌ No se encontró ningún reclamo con ese Nº o DNI']);
}
?>