<?php
$adminTitle = 'Productos | Admin';
require_once '_header.php';

$accion = $_GET['accion'] ?? 'listar';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$categorias = obtenerCategorias($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idProducto = (int)($_POST['id_producto'] ?? 0);
    $datos = [
        ':id_categoria' => (int)$_POST['id_categoria'],
        ':nombre_producto' => trim($_POST['nombre_producto']),
        ':descripcion' => trim($_POST['descripcion']),
        ':precio' => (float)$_POST['precio'],
        ':precio_oferta' => $_POST['precio_oferta'] !== '' ? (float)$_POST['precio_oferta'] : null,
        ':cantidad_stock' => (int)$_POST['cantidad_stock'],
        ':stock_minimo' => (int)$_POST['stock_minimo'],
        ':tipo_stock' => $_POST['tipo_stock'],
        ':imagen' => trim($_POST['imagen']),
        ':destacado' => isset($_POST['destacado']) ? 1 : 0,
        ':estado' => isset($_POST['estado']) ? 1 : 0,
    ];
    if ($idProducto > 0) {
        $datos[':id_producto'] = $idProducto;
        $sql = "UPDATE productos SET id_categoria=:id_categoria, nombre_producto=:nombre_producto, descripcion=:descripcion, precio=:precio, precio_oferta=:precio_oferta, cantidad_stock=:cantidad_stock, stock_minimo=:stock_minimo, tipo_stock=:tipo_stock, imagen=:imagen, destacado=:destacado, estado=:estado WHERE id_producto=:id_producto";
    } else {
        $sql = "INSERT INTO productos (id_categoria, nombre_producto, descripcion, precio, precio_oferta, cantidad_stock, stock_minimo, tipo_stock, imagen, destacado, estado) VALUES (:id_categoria, :nombre_producto, :descripcion, :precio, :precio_oferta, :cantidad_stock, :stock_minimo, :tipo_stock, :imagen, :destacado, :estado)";
    }
    $pdo->prepare($sql)->execute($datos);
    header('Location: productos.php');
    exit;
}

if ($accion === 'eliminar' && $id > 0) {
    $pdo->prepare("UPDATE productos SET estado = 0 WHERE id_producto = :id")->execute([':id' => $id]);
    header('Location: productos.php');
    exit;
}

$producto = null;
if (($accion === 'editar' || $accion === 'nuevo') && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id_producto = :id");
    $stmt->execute([':id' => $id]);
    $producto = $stmt->fetch();
}

if ($accion === 'nuevo' || $accion === 'editar'):
?>
<h1 class="h3 fw-bold mb-4"><?= $producto ? 'Editar producto' : 'Nuevo producto' ?></h1>
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" class="row g-3">
    <input type="hidden" name="id_producto" value="<?= (int)($producto['id_producto'] ?? 0) ?>">
    <div class="col-md-4"><label class="form-label">Categoría</label><select name="id_categoria" class="form-select" required><?php foreach ($categorias as $cat): ?><option value="<?= (int)$cat['id_categoria'] ?>" <?= (int)($producto['id_categoria'] ?? 0) === (int)$cat['id_categoria'] ? 'selected' : '' ?>><?= limpiarTexto($cat['nombre_categoria']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-8"><label class="form-label">Nombre</label><input name="nombre_producto" class="form-control" value="<?= limpiarTexto($producto['nombre_producto'] ?? '') ?>" required></div>
    <div class="col-12"><label class="form-label">Descripción</label><textarea name="descripcion" class="form-control" rows="3"><?= limpiarTexto($producto['descripcion'] ?? '') ?></textarea></div>
    <div class="col-md-3"><label class="form-label">Precio</label><input type="number" name="precio" class="form-control" value="<?= limpiarTexto($producto['precio'] ?? 0) ?>" required></div>
    <div class="col-md-3"><label class="form-label">Precio oferta</label><input type="number" name="precio_oferta" class="form-control" value="<?= limpiarTexto($producto['precio_oferta'] ?? '') ?>"></div>
    <div class="col-md-3"><label class="form-label">Stock</label><input type="number" name="cantidad_stock" class="form-control" value="<?= limpiarTexto($producto['cantidad_stock'] ?? 0) ?>"></div>
    <div class="col-md-3"><label class="form-label">Stock mínimo</label><input type="number" name="stock_minimo" class="form-control" value="<?= limpiarTexto($producto['stock_minimo'] ?? 5) ?>"></div>
    <div class="col-md-4"><label class="form-label">Tipo stock</label><select name="tipo_stock" class="form-select"><option value="unidad" <?= ($producto['tipo_stock'] ?? '') === 'unidad' ? 'selected' : '' ?>>Unidad</option><option value="ilimitado" <?= ($producto['tipo_stock'] ?? '') === 'ilimitado' ? 'selected' : '' ?>>Ilimitado</option></select></div>
    <div class="col-md-8"><label class="form-label">Imagen</label><input name="imagen" class="form-control" value="<?= limpiarTexto($producto['imagen'] ?? '') ?>" placeholder="assets/img/productos/pizza.jpg o URL"></div>
    <div class="col-md-3 form-check ms-2"><input class="form-check-input" type="checkbox" name="destacado" <?= !empty($producto['destacado']) ? 'checked' : '' ?>><label class="form-check-label">Destacado</label></div>
    <div class="col-md-3 form-check"><input class="form-check-input" type="checkbox" name="estado" <?= !isset($producto['estado']) || (int)$producto['estado'] === 1 ? 'checked' : '' ?>><label class="form-check-label">Activo</label></div>
    <div class="col-12"><button class="btn btn-danger">Guardar</button> <a href="productos.php" class="btn btn-outline-secondary">Cancelar</a></div>
</form>
</div></div>
<?php else:
$productos = $pdo->query("SELECT p.*, c.nombre_categoria FROM productos p INNER JOIN categorias c ON c.id_categoria = p.id_categoria ORDER BY p.id_producto DESC")->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-4"><h1 class="h3 fw-bold">Productos</h1><a class="btn btn-danger" href="productos.php?accion=nuevo">Nuevo producto</a></div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>ID</th><th>Producto</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Estado</th><th></th></tr></thead><tbody>
<?php foreach ($productos as $p): ?><tr><td><?= (int)$p['id_producto'] ?></td><td><?= limpiarTexto($p['nombre_producto']) ?></td><td><?= limpiarTexto($p['nombre_categoria']) ?></td><td><?= formatoGs($p['precio_oferta'] ?: $p['precio']) ?></td><td><?= limpiarTexto($p['tipo_stock'] === 'ilimitado' ? 'Ilimitado' : $p['cantidad_stock']) ?></td><td><?= (int)$p['estado'] === 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="productos.php?accion=editar&id=<?= (int)$p['id_producto'] ?>">Editar</a> <a class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Desactivar producto?')" href="productos.php?accion=eliminar&id=<?= (int)$p['id_producto'] ?>">Desactivar</a></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php endif; require_once '_footer.php'; ?>
