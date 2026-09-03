<?php
$adminTitle = 'Pedidos | Admin';
require_once '_header.php';

$estados = obtenerEstadosPedido($pdo);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPedido = (int)($_POST['id_pedido'] ?? 0);
    $idEstado = (int)($_POST['id_estado_pedido'] ?? 1);
    $estadoPago = $_POST['estado_pago'] ?? 'pendiente';
    if ($idPedido > 0) {
        $pdo->prepare("UPDATE pedidos SET id_estado_pedido = :estado, id_usuario = :usuario WHERE id_pedido = :pedido")->execute([':estado' => $idEstado, ':usuario' => $_SESSION['admin_id'], ':pedido' => $idPedido]);
        $pdo->prepare("UPDATE pagos SET estado_pago = :estado_pago WHERE id_pedido = :pedido")->execute([':estado_pago' => $estadoPago, ':pedido' => $idPedido]);
    }
    header('Location: pedidos.php');
    exit;
}

$ver = isset($_GET['ver']) ? (int)$_GET['ver'] : 0;
if ($ver > 0) {
    $stmt = $pdo->prepare("SELECT p.*, c.nombre, c.apellido, c.telefono, c.correo, d.direccion, d.referencia, e.nombre_estado, mp.nombre_metodo, pa.estado_pago FROM pedidos p INNER JOIN clientes c ON c.id_cliente = p.id_cliente LEFT JOIN direcciones_cliente d ON d.id_direccion = p.id_direccion INNER JOIN estados_pedido e ON e.id_estado_pedido = p.id_estado_pedido LEFT JOIN pagos pa ON pa.id_pedido = p.id_pedido LEFT JOIN metodos_pago mp ON mp.id_metodo_pago = pa.id_metodo_pago WHERE p.id_pedido = :id");
    $stmt->execute([':id' => $ver]);
    $pedido = $stmt->fetch();
    if (!$pedido) { header('Location: pedidos.php'); exit; }
    $stmt = $pdo->prepare("SELECT dp.*, pr.nombre_producto FROM detalle_pedido dp INNER JOIN productos pr ON pr.id_producto = dp.id_producto WHERE dp.id_pedido = :id");
    $stmt->execute([':id' => $ver]);
    $detalles = $stmt->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-4"><h1 class="h3 fw-bold">Pedido #<?= (int)$pedido['id_pedido'] ?></h1><a href="pedidos.php" class="btn btn-outline-secondary">Volver</a></div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white fw-bold">Detalle</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody><?php foreach ($detalles as $d): ?><tr><td><?= limpiarTexto($d['nombre_producto']) ?></td><td><?= (int)$d['cantidad'] ?></td><td><?= formatoGs($d['precio_unitario']) ?></td><td><?= formatoGs($d['subtotal']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
        <div class="card border-0 shadow-sm"><div class="card-body"><h5 class="fw-bold">Cliente</h5><p class="mb-1"><?= limpiarTexto(trim($pedido['nombre'] . ' ' . $pedido['apellido'])) ?></p><p class="mb-1">Tel: <?= limpiarTexto($pedido['telefono']) ?></p><p class="mb-1">Entrega: <?= limpiarTexto($pedido['tipo_entrega']) ?></p><p class="mb-0">Dirección: <?= limpiarTexto($pedido['direccion'] ?? 'Retiro del local') ?></p></div></div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm"><div class="card-body">
            <h5 class="fw-bold">Actualizar pedido</h5>
            <form method="POST">
                <input type="hidden" name="id_pedido" value="<?= (int)$pedido['id_pedido'] ?>">
                <label class="form-label">Estado del pedido</label>
                <select name="id_estado_pedido" class="form-select mb-3"><?php foreach ($estados as $e): ?><option value="<?= (int)$e['id_estado_pedido'] ?>" <?= (int)$pedido['id_estado_pedido'] === (int)$e['id_estado_pedido'] ? 'selected' : '' ?>><?= limpiarTexto($e['nombre_estado']) ?></option><?php endforeach; ?></select>
                <label class="form-label">Estado del pago</label>
                <select name="estado_pago" class="form-select mb-3"><option value="pendiente" <?= $pedido['estado_pago'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option><option value="pagado" <?= $pedido['estado_pago'] === 'pagado' ? 'selected' : '' ?>>Pagado</option><option value="rechazado" <?= $pedido['estado_pago'] === 'rechazado' ? 'selected' : '' ?>>Rechazado</option><option value="anulado" <?= $pedido['estado_pago'] === 'anulado' ? 'selected' : '' ?>>Anulado</option></select>
                <div class="border-top pt-3"><div class="d-flex justify-content-between"><span>Subtotal</span><b><?= formatoGs($pedido['subtotal']) ?></b></div><div class="d-flex justify-content-between"><span>Delivery</span><b><?= formatoGs($pedido['costo_delivery']) ?></b></div><div class="d-flex justify-content-between fs-5"><span>Total</span><b><?= formatoGs($pedido['total']) ?></b></div></div>
                <button class="btn btn-danger w-100 mt-3">Guardar cambios</button>
            </form>
        </div></div>
    </div>
</div>
<?php } else {
$pedidos = $pdo->query("SELECT p.id_pedido, p.total, p.fecha_pedido, c.nombre, c.telefono, e.nombre_estado, pa.estado_pago FROM pedidos p INNER JOIN clientes c ON c.id_cliente = p.id_cliente INNER JOIN estados_pedido e ON e.id_estado_pedido = p.id_estado_pedido LEFT JOIN pagos pa ON pa.id_pedido = p.id_pedido ORDER BY p.id_pedido DESC")->fetchAll();
?>
<h1 class="h3 fw-bold mb-4">Pedidos</h1>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>#</th><th>Cliente</th><th>Teléfono</th><th>Estado</th><th>Pago</th><th>Total</th><th>Fecha</th><th></th></tr></thead><tbody>
<?php foreach ($pedidos as $p): ?><tr><td><?= (int)$p['id_pedido'] ?></td><td><?= limpiarTexto($p['nombre']) ?></td><td><?= limpiarTexto($p['telefono']) ?></td><td><?= limpiarTexto($p['nombre_estado']) ?></td><td><?= limpiarTexto($p['estado_pago'] ?? 'pendiente') ?></td><td><?= formatoGs($p['total']) ?></td><td><?= limpiarTexto($p['fecha_pedido']) ?></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="pedidos.php?ver=<?= (int)$p['id_pedido'] ?>">Ver</a></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php } require_once '_footer.php'; ?>
