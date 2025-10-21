<?php
header('Content-Type: application/json');
include 'db.php';

$input = json_decode(file_get_contents('php://input'), true);

$numero_ticket = $input['id'] ?? '';
$estado = $input['estado'] ?? '';

if (!$numero_ticket || !$estado) {
    echo json_encode(['error' => 'Faltan datos']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE reclamos SET estado = ? WHERE numero_ticket = ?");
    $rows = $stmt->execute([$estado, $numero_ticket]);

    if ($rows) {
        echo json_encode(['success' => true, 'message' => "Reclamo #$numero_ticket actualizado a '$estado'"]);
    } else {
        echo json_encode(['error' => 'Reclamo no encontrado']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
?>