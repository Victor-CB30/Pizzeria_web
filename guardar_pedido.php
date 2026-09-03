<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) throw new Exception('Datos inválidos.');

    $items = $data['items'] ?? [];
    if (empty($items)) throw new Exception('El carrito está vacío.');

    $nombreCompleto = trim($data['cliente'] ?? 'Cliente');
    $telefono = trim($data['telefono'] ?? 'Sin teléfono');
    $direccion = trim($data['direccion'] ?? 'Sin dirección indicada');
    $tipoEntrega = ($data['tipo_entrega'] ?? 'delivery') === 'retiro_local' ? 'retiro_local' : 'delivery';
    $metodoPagoNombre = trim($data['metodo_pago'] ?? 'Efectivo');

    $partes = preg_split('/\s+/', $nombreCompleto, 2);
    $nombre = $partes[0] ?: 'Cliente';
    $apellido = $partes[1] ?? '';

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT id_cliente FROM clientes WHERE telefono = :telefono LIMIT 1");
    $stmt->execute([':telefono' => $telefono]);
    $cliente = $stmt->fetch();

    if ($cliente) {
        $idCliente = (int)$cliente['id_cliente'];
        $stmt = $pdo->prepare("UPDATE clientes SET nombre = :nombre, apellido = :apellido WHERE id_cliente = :id");
        $stmt->execute([':nombre' => $nombre, ':apellido' => $apellido, ':id' => $idCliente]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO clientes (nombre, apellido, telefono) VALUES (:nombre, :apellido, :telefono)");
        $stmt->execute([':nombre' => $nombre, ':apellido' => $apellido, ':telefono' => $telefono]);
        $idCliente = (int)$pdo->lastInsertId();
    }

    $idDireccion = null;
    if ($tipoEntrega === 'delivery') {
        $stmt = $pdo->prepare("INSERT INTO direcciones_cliente (id_cliente, direccion, ciudad, referencia, es_principal) VALUES (:cliente, :direccion, 'Ciudad del Este', :referencia, 1)");
        $stmt->execute([':cliente' => $idCliente, ':direccion' => $direccion, ':referencia' => $direccion]);
        $idDireccion = (int)$pdo->lastInsertId();
    }

    $subtotal = 0;
    $detalle = [];
    foreach ($items as $item) {
        $idProducto = (int)($item['id'] ?? 0);
        $cantidad = max(1, (int)($item['qty'] ?? 1));

        $producto = obtenerProductoPorId($pdo, $idProducto);
        if (!$producto) continue;

        $precio = !empty($producto['precio_oferta']) ? (float)$producto['precio_oferta'] : (float)$producto['precio'];
        $linea = $precio * $cantidad;
        $subtotal += $linea;
        $detalle[] = ['id_producto' => $idProducto, 'cantidad' => $cantidad, 'precio' => $precio, 'subtotal' => $linea];
    }

    if (empty($detalle)) throw new Exception('No se pudieron validar los productos.');

    $costoDelivery = $tipoEntrega === 'delivery' ? 10000 : 0;
    $total = $subtotal + $costoDelivery;

    $stmt = $pdo->prepare("INSERT INTO pedidos (id_cliente, id_direccion, id_estado_pedido, tipo_entrega, subtotal, costo_delivery, descuento, total, observacion) VALUES (:cliente, :direccion, 1, :tipo, :subtotal, :delivery, 0, :total, :obs)");
    $stmt->execute([
        ':cliente' => $idCliente,
        ':direccion' => $idDireccion,
        ':tipo' => $tipoEntrega,
        ':subtotal' => $subtotal,
        ':delivery' => $costoDelivery,
        ':total' => $total,
        ':obs' => 'Pedido generado desde la web'
    ]);
    $idPedido = (int)$pdo->lastInsertId();

    $stmtDetalle = $pdo->prepare("INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario, subtotal) VALUES (:pedido, :producto, :cantidad, :precio, :subtotal)");
    foreach ($detalle as $d) {
        $stmtDetalle->execute([
            ':pedido' => $idPedido,
            ':producto' => $d['id_producto'],
            ':cantidad' => $d['cantidad'],
            ':precio' => $d['precio'],
            ':subtotal' => $d['subtotal']
        ]);
    }

    $stmt = $pdo->prepare("SELECT id_metodo_pago FROM metodos_pago WHERE nombre_metodo = :nombre LIMIT 1");
    $stmt->execute([':nombre' => $metodoPagoNombre]);
    $metodo = $stmt->fetch();
    $idMetodo = $metodo ? (int)$metodo['id_metodo_pago'] : 1;

    $stmt = $pdo->prepare("INSERT INTO pagos (id_pedido, id_metodo_pago, monto, referencia_pago, estado_pago) VALUES (:pedido, :metodo, :monto, :referencia, 'pendiente')");
    $stmt->execute([':pedido' => $idPedido, ':metodo' => $idMetodo, ':monto' => $total, ':referencia' => 'Pendiente de confirmación']);

    $pdo->commit();
    echo json_encode(['ok' => true, 'id_pedido' => $idPedido, 'total' => $total]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
}
