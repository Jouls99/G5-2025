<?php
header('Content-Type: application/json');

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Verificar campos obligatorios
if (!isset($_POST['titulo']) || empty($_POST['titulo'])) {
    echo json_encode(['success' => false, 'error' => 'Título requerido']);
    exit;
}

$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'] ?? '';
$activa = isset($_POST['activa']) ? 1 : 0;

// Manejo de la imagen
$imagen_url = null;
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['imagen']['tmp_name'];
    $name = basename($_FILES['imagen']['name']);
    $upload_dir = 'uploads/';
    $target_path = $upload_dir . uniqid() . '_' . $name;

    if (move_uploaded_file($tmp_name, $target_path)) {
        $imagen_url = $target_path;
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al subir la imagen']);
        exit;
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO ofertas (titulo, descripcion, imagen_url, activa) VALUES (?, ?, ?, ?)");
    $stmt->execute([$titulo, $descripcion, $imagen_url, $activa]);

    echo json_encode([
        'success' => true,
        'message' => 'Oferta agregada correctamente',
        'id' => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>