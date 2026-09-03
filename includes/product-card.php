<?php
$precioFinal = !empty($producto['precio_oferta']) ? $producto['precio_oferta'] : $producto['precio'];
$imagen = assetPath($producto['imagen'] ?? '', 'assets/img/logo/placeholder.svg');
?>
<div class="col-sm-6 col-lg-3">
    <div class="product-card">
        <div class="product-img-wrap">
            <img src="<?= limpiarTexto($imagen) ?>"
                 onerror="this.onerror=null;this.src='assets/img/logo/placeholder.svg';"
                 alt="<?= limpiarTexto($producto['nombre_producto']) ?>">
            <?php if ((int)$producto['destacado'] === 1): ?>
                <span class="badge-featured">Destacado</span>
            <?php endif; ?>
        </div>
        <div class="product-body">
            <span class="product-category"><?= limpiarTexto($producto['nombre_categoria']) ?></span>
            <h5><?= limpiarTexto($producto['nombre_producto']) ?></h5>
            <p><?= limpiarTexto(mb_strimwidth($producto['descripcion'] ?? '', 0, 80, '…')) ?></p>
            <div class="price-row mt-auto">
                <div>
                    <?php if (!empty($producto['precio_oferta'])): ?>
                        <strong><?= formatoGs($producto['precio_oferta']) ?></strong>
                        <del class="d-block"><?= formatoGs($producto['precio']) ?></del>
                    <?php else: ?>
                        <strong><?= formatoGs($producto['precio']) ?></strong>
                    <?php endif; ?>
                </div>
                <a href="producto.php?id=<?= (int)$producto['id_producto'] ?>" class="btn btn-sm btn-outline-dark">Ver</a>
            </div>
            <button class="btn btn-order w-100 mt-3"
                    onclick="addToCart({id:<?= (int)$producto['id_producto'] ?>,name:'<?= limpiarTexto($producto['nombre_producto']) ?>',price:<?= (float)$precioFinal ?>,image:'<?= limpiarTexto($imagen) ?>'})">
                <i class="bi bi-cart-plus"></i> Agregar
            </button>
        </div>
    </div>
</div>
