<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
include 'db.php';

// Verificar si se recibió el ID
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID de reclamo no proporcionado']);
    exit;
}

$id = $_POST['id'];

try {
    // Preparar la consulta para eliminar
    $stmt = $pdo->prepare("DELETE FROM reclamos WHERE numero_ticket = ?");
    $stmt->execute([$id]);

    // Verificar si se eliminó algo
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Reclamo eliminado correctamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se encontró el reclamo con ese ID']);
    }

} catch (PDOException $e) {
    // En caso de error, devolver mensaje claro
    echo json_encode(['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
