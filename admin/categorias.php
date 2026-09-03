<?php
$adminTitle = 'Categorías | Admin';
require_once '_header.php';
$accion = $_GET['accion'] ?? 'listar';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCategoria = (int)($_POST['id_categoria'] ?? 0);
    $datos = [':nombre' => trim($_POST['nombre_categoria']), ':descripcion' => trim($_POST['descripcion']), ':imagen' => trim($_POST['imagen']), ':estado' => isset($_POST['estado']) ? 1 : 0];
    if ($idCategoria > 0) {
        $datos[':id'] = $idCategoria;
        $sql = "UPDATE categorias SET nombre_categoria=:nombre, descripcion=:descripcion, imagen=:imagen, estado=:estado WHERE id_categoria=:id";
    } else {
        $sql = "INSERT INTO categorias (nombre_categoria, descripcion, imagen, estado) VALUES (:nombre, :descripcion, :imagen, :estado)";
    }
    $pdo->prepare($sql)->execute($datos);
    header('Location: categorias.php');
    exit;
}

$categoria = null;
if (($accion === 'editar' || $accion === 'nuevo') && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id_categoria = :id");
    $stmt->execute([':id' => $id]);
    $categoria = $stmt->fetch();
}

if ($accion === 'nuevo' || $accion === 'editar'):
?>
<h1 class="h3 fw-bold mb-4"><?= $categoria ? 'Editar categoría' : 'Nueva categoría' ?></h1>
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" class="row g-3">
<input type="hidden" name="id_categoria" value="<?= (int)($categoria['id_categoria'] ?? 0) ?>">
<div class="col-md-6"><label class="form-label">Nombre</label><input name="nombre_categoria" class="form-control" value="<?= limpiarTexto($categoria['nombre_categoria'] ?? '') ?>" required></div>
<div class="col-md-6"><label class="form-label">Imagen</label><input name="imagen" class="form-control" value="<?= limpiarTexto($categoria['imagen'] ?? '') ?>"></div>
<div class="col-12"><label class="form-label">Descripción</label><textarea name="descripcion" class="form-control"><?= limpiarTexto($categoria['descripcion'] ?? '') ?></textarea></div>
<div class="col-12 form-check ms-2"><input class="form-check-input" type="checkbox" name="estado" <?= !isset($categoria['estado']) || (int)$categoria['estado'] === 1 ? 'checked' : '' ?>><label class="form-check-label">Activo</label></div>
<div class="col-12"><button class="btn btn-danger">Guardar</button> <a href="categorias.php" class="btn btn-outline-secondary">Cancelar</a></div>
</form></div></div>
<?php else: $categorias = $pdo->query("SELECT * FROM categorias ORDER BY nombre_categoria ASC")->fetchAll(); ?>
<div class="d-flex justify-content-between align-items-center mb-4"><h1 class="h3 fw-bold">Categorías</h1><a class="btn btn-danger" href="categorias.php?accion=nuevo">Nueva categoría</a></div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Estado</th><th></th></tr></thead><tbody>
<?php foreach ($categorias as $c): ?><tr><td><?= (int)$c['id_categoria'] ?></td><td><?= limpiarTexto($c['nombre_categoria']) ?></td><td><?= limpiarTexto($c['descripcion']) ?></td><td><?= (int)$c['estado'] === 1 ? '<span class="badge bg-success">Activa</span>' : '<span class="badge bg-secondary">Inactiva</span>' ?></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="categorias.php?accion=editar&id=<?= (int)$c['id_categoria'] ?>">Editar</a></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php endif; require_once '_footer.php'; ?>
