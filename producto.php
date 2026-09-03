<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$producto = obtenerProductoPorId($pdo, $id);
if (!$producto) {
    header('Location: menu.php');
    exit;
}

$pageTitle = $producto['nombre_producto'] . ' | Pizzería';
require_once 'includes/header.php';
$precioFinal = !empty($producto['precio_oferta']) ? $producto['precio_oferta'] : $producto['precio'];
$imagen = assetPath($producto['imagen'], 'assets/img/logo/placeholder.svg');
?>
<section class="page-header">
    <div class="container">
        <h1><?= limpiarTexto($producto['nombre_producto']) ?></h1>
        <p><?= limpiarTexto($producto['nombre_categoria']) ?></p>
    </div>
</section>
<section class="container py-5">
    <div class="row g-5 align-items-center">
        <div class="col-lg-6">
            <img class="detail-img" src="<?= limpiarTexto($imagen) ?>" onerror="this.onerror=null;this.src='assets/img/logo/placeholder.svg';" alt="<?= limpiarTexto($producto['nombre_producto']) ?>">
        </div>
        <div class="col-lg-6">
            <span class="product-category"><?= limpiarTexto($producto['nombre_categoria']) ?></span>
            <h2><?= limpiarTexto($producto['nombre_producto']) ?></h2>
            <p class="lead"><?= limpiarTexto($producto['descripcion']) ?></p>
            <div class="detail-price mb-3">
                <?php if (!empty($producto['precio_oferta'])): ?>
                    <span><?= formatoGs($producto['precio_oferta']) ?></span>
                    <del><?= formatoGs($producto['precio']) ?></del>
                <?php else: ?>
                    <span><?= formatoGs($producto['precio']) ?></span>
                <?php endif; ?>
            </div>
            <div class="d-flex gap-3 flex-wrap">
                <button class="btn btn-order btn-lg" onclick="addToCart({id: <?= (int)$producto['id_producto'] ?>, name: '<?= limpiarTexto($producto['nombre_producto']) ?>', price: <?= (float)$precioFinal ?>, image: '<?= limpiarTexto($imagen) ?>'})">
                    <i class="bi bi-cart-plus"></i> Agregar al carrito
                </button>
                <a class="btn btn-outline-dark btn-lg" href="menu.php">Volver al menú</a>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
