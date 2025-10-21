<?php
header('Content-Type: application/json');

include 'db.php';

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID inválido']);
    exit;
}

$id = $_POST['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM ofertas WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Oferta eliminada']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Oferta no encontrada']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>