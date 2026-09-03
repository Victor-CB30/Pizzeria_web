<?php
$pageTitle = 'Inicio | Pizzería';
require_once 'includes/header.php';
$destacados = obtenerDestacados($pdo);
$categorias = obtenerCategorias($pdo);
?>

<!-- HERO -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="hero-badge"><i class="bi bi-fire"></i> Pedido rápido por WhatsApp</span>
                <h1><?= limpiarTexto($empresa['nombre_empresa']) ?></h1>
                <p><?= limpiarTexto($empresa['slogan'] ?? 'Las mejores pizzas y hamburguesas para compartir.') ?></p>
                <div class="d-flex gap-3 flex-wrap mt-4">
                    <a href="menu.php" class="btn btn-order btn-lg">Ver menú</a>
                    <button class="btn btn-outline-light btn-lg" data-bs-toggle="offcanvas" data-bs-target="#cartCanvas">
                        <i class="bi bi-cart3"></i> Carrito
                    </button>
                </div>
            </div>
            <div class="col-lg-6 text-center d-none d-lg-block">
                <img class="hero-img"
                     src="<?= limpiarTexto(assetPath($empresa['imagen_portada'] ?? DEFAULT_HERO, DEFAULT_HERO)) ?>"
                     alt="Producto principal">
            </div>
        </div>
    </div>
</section>

<!-- CATEGORÍAS -->
<section class="container py-5">
    <div class="d-flex flex-column align-items-center text-center mb-4">
        <span class="section-label">Explorá el menú</span>
        <h2 class="section-title">Categorías</h2>
    </div>
    <div class="row g-3">
        <?php foreach ($categorias as $cat): ?>
        <div class="col-sm-6 col-lg-4">
            <a class="category-card" href="menu.php?categoria=<?= (int)$cat['id_categoria'] ?>">
                <img src="<?= limpiarTexto(assetPath($cat['imagen'], 'assets/img/logo/placeholder.svg')) ?>"
                     alt="<?= limpiarTexto($cat['nombre_categoria']) ?>">
                <div>
                    <h5><?= limpiarTexto($cat['nombre_categoria']) ?></h5>
                    <p><?= limpiarTexto($cat['descripcion']) ?></p>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- DESTACADOS -->
<?php if (!empty($destacados)): ?>
<section class="featured-section py-5">
    <div class="container">
        <div class="d-flex flex-column align-items-center text-center mb-4">
            <span class="section-label">Favoritos de la casa</span>
            <h2 class="section-title">Productos destacados</h2>
        </div>
        <div class="row g-4">
            <?php foreach ($destacados as $producto): ?>
                <?php include 'includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA DELIVERY -->
<section class="container py-5">
    <div class="delivery-box">
        <div>
            <h3>Armá tu pedido directo al WhatsApp</h3>
            <p>Seleccioná productos, ajustá cantidades y confirmá con un mensaje automático.</p>
        </div>
        <a href="menu.php" class="btn btn-order btn-lg mt-3 mt-md-0">Pedir ahora</a>
    </div>
</section>
<!--   -->
<?php require_once 'includes/footer.php'; ?>
