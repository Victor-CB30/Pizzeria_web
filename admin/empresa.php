<?php
$adminTitle = 'Empresa | Admin';
require_once '_header.php';

$empresa = obtenerEmpresa($pdo);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->query("SELECT id_empresa FROM informacion_empresa ORDER BY id_empresa ASC LIMIT 1");
    $row = $stmt->fetch();
    $datos = [
        ':nombre_empresa' => trim($_POST['nombre_empresa']),
        ':slogan' => trim($_POST['slogan']),
        ':telefono' => trim($_POST['telefono']),
        ':correo' => trim($_POST['correo']),
        ':direccion' => trim($_POST['direccion']),
        ':horario' => trim($_POST['horario']),
        ':logo' => trim($_POST['logo']),
        ':imagen_portada' => trim($_POST['imagen_portada']),
    ];
    if ($row) {
        $datos[':id'] = $row['id_empresa'];
        $sql = "UPDATE informacion_empresa SET nombre_empresa=:nombre_empresa, slogan=:slogan, telefono=:telefono, correo=:correo, direccion=:direccion, horario=:horario, logo=:logo, imagen_portada=:imagen_portada WHERE id_empresa=:id";
    } else {
        $sql = "INSERT INTO informacion_empresa (nombre_empresa, slogan, telefono, correo, direccion, horario, logo, imagen_portada) VALUES (:nombre_empresa, :slogan, :telefono, :correo, :direccion, :horario, :logo, :imagen_portada)";
    }
    $pdo->prepare($sql)->execute($datos);
    header('Location: empresa.php?ok=1');
    exit;
}
?>
<h1 class="h3 fw-bold mb-4">Información de la empresa</h1>
<?php if (isset($_GET['ok'])): ?><div class="alert alert-success">Datos actualizados.</div><?php endif; ?>
<div class="card border-0 shadow-sm"><div class="card-body"><form method="POST" class="row g-3">
<div class="col-md-6"><label class="form-label">Nombre</label><input name="nombre_empresa" class="form-control" value="<?= limpiarTexto($empresa['nombre_empresa']) ?>" required></div>
<div class="col-md-6"><label class="form-label">Slogan</label><input name="slogan" class="form-control" value="<?= limpiarTexto($empresa['slogan']) ?>"></div>
<div class="col-md-4"><label class="form-label">Teléfono WhatsApp</label><input name="telefono" class="form-control" value="<?= limpiarTexto($empresa['telefono']) ?>"></div>
<div class="col-md-4"><label class="form-label">Correo</label><input name="correo" class="form-control" value="<?= limpiarTexto($empresa['correo']) ?>"></div>
<div class="col-md-4"><label class="form-label">Horario</label><input name="horario" class="form-control" value="<?= limpiarTexto($empresa['horario']) ?>"></div>
<div class="col-12"><label class="form-label">Dirección</label><input name="direccion" class="form-control" value="<?= limpiarTexto($empresa['direccion']) ?>"></div>
<div class="col-md-6"><label class="form-label">Logo</label><input name="logo" class="form-control" value="<?= limpiarTexto($empresa['logo']) ?>"></div>
<div class="col-md-6"><label class="form-label">Imagen portada</label><input name="imagen_portada" class="form-control" value="<?= limpiarTexto($empresa['imagen_portada']) ?>"></div>
<div class="col-12"><button class="btn btn-danger">Guardar</button></div>
</form></div></div>
<?php require_once '_footer.php'; ?>
