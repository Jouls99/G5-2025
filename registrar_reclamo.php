

<?php
header('Content-Type: application/json');
include 'db.php';

$input = json_decode(file_get_contents('php://input'), true);

$nombre = $input['nombre'] ?? '';
$apellido = $input['apellido'] ?? '';
$dni = $input['dni'] ?? '';
$localidad_nombre = $input['localidad'] ?? '';
$descripcion = $input['descripcion'] ?? '';
$respuestas = $input['respuestas'] ?? [];

if (!$nombre || !$apellido || !$dni || !$localidad_nombre || !$descripcion) {
    echo json_encode(['error' => 'Todos los campos son obligatorios']);
    exit;
}

try {
    // 1. Obtener o crear localidad
    $stmt = $pdo->prepare("SELECT id FROM localidades WHERE nombre = ?");
    $stmt->execute([$localidad_nombre]);
    $localidad = $stmt->fetch();

    if (!$localidad) {
        $stmt = $pdo->prepare("INSERT INTO localidades (nombre) VALUES (?)");
        $stmt->execute([$localidad_nombre]);
        $localidad_id = $pdo->lastInsertId();
    } else {
        $localidad_id = $localidad['id'];
    }

    // 2. Buscar o crear cliente
    $stmt = $pdo->prepare("SELECT id FROM clientes WHERE dni = ?");
    $stmt->execute([$dni]);
    $cliente = $stmt->fetch();

    if (!$cliente) {
        $stmt = $pdo->prepare("
            INSERT INTO clientes (nombre, apellido, dni, localidad_id)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$nombre, $apellido, $dni, $localidad_id]);
        $cliente_id = $pdo->lastInsertId();
    } else {
        $cliente_id = $cliente['id'];
    }

    // 3. Generar número de ticket
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM reclamos");
    $total = $stmt->fetch()['total'] + 1;
    $numero_ticket = 'FIO-' . str_pad($total, 3, '0', STR_PAD_LEFT);

    // 4. Insertar reclamo
    $stmt = $pdo->prepare("
        INSERT INTO reclamos (numero_ticket, cliente_id, descripcion, respuestas)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$numero_ticket, $cliente_id, $descripcion, json_encode($respuestas)]);

    echo json_encode([
        'success' => true,
        'message' => "✅ Reclamo #$numero_ticket registrado con éxito",
        'id' => $numero_ticket
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => 'Error al registrar: ' . $e->getMessage()]);
}
?>