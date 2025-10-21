<?php
header('Content-Type: application/json');

include 'db.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM ofertas WHERE activa = 1 ORDER BY fecha_publicacion DESC");
    $stmt->execute();
    $ofertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($ofertas);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al obtener ofertas: ' . $e->getMessage()]);
}
?>