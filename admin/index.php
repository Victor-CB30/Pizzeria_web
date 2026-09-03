<?php
$adminTitle = 'Dashboard | Admin';
require_once '_header.php';
$totales = [
    'productos' => $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn(),
    'categorias' => $pdo->query("SELECT COUNT(*) FROM categorias")->fetchColumn(),
    'pedidos' => $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn(),
    'pendientes' => $pdo->query("SELECT COUNT(*) FROM pedidos WHERE id_estado_pedido = 1")->fetchColumn(),
];
$ultimos = $pdo->query("SELECT p.id_pedido, p.total, p.fecha_pedido, c.nombre, c.telefono, e.nombre_estado FROM pedidos p INNER JOIN clientes c ON c.id_cliente = p.id_cliente INNER JOIN estados_pedido e ON e.id_estado_pedido = p.id_estado_pedido ORDER BY p.id_pedido DESC LIMIT 8")->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold">Dashboard</h1>
</div>
<div class="row g-3 mb-4">
    <?php foreach ($totales as $titulo => $valor): ?>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted text-uppercase small"><?= limpiarTexto($titulo) ?></div><div class="fs-2 fw-bold"><?= (int)$valor ?></div></div></div></div>
    <?php endforeach; ?>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-bold">Últimos pedidos</div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>#</th><th>Cliente</th><th>Teléfono</th><th>Estado</th><th>Total</th><th>Fecha</th></tr></thead>
            <tbody>
            <?php foreach ($ultimos as $p): ?>
                <tr><td><?= (int)$p['id_pedido'] ?></td><td><?= limpiarTexto($p['nombre']) ?></td><td><?= limpiarTexto($p['telefono']) ?></td><td><span class="badge bg-warning text-dark"><?= limpiarTexto($p['nombre_estado']) ?></span></td><td><?= formatoGs($p['total']) ?></td><td><?= limpiarTexto($p['fecha_pedido']) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '_footer.php'; ?>
