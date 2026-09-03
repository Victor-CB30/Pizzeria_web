<?php
$pageTitle = 'Menú | Pizzería';
require_once 'includes/header.php';

$buscar   = isset($_GET['buscar'])   ? trim($_GET['buscar'])   : '';
$categoria = isset($_GET['categoria']) && $_GET['categoria'] !== '' ? (int)$_GET['categoria'] : 0;
$productos  = obtenerProductos($pdo, $categoria, $buscar);
$categorias = obtenerCategorias($pdo);
?>
<section class="page-header">
    <div class="container">
        <h1><?= $buscar !== '' ? 'Resultados: ' . limpiarTexto($buscar) : 'Nuestro Menú' ?></h1>
        <p>Elegí tus productos y agregalos al carrito.</p>
    </div>
</section>

<section class="container py-5">

    <!-- Filtro -->
    <div class="menu-filter mb-5">
        <!-- Buscador -->
        <div class="mb-3">
            <label class="form-label">Buscar producto</label>
            <div class="position-relative">
                <i class="bi bi-search position-absolute" style="left:.85rem;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none"></i>
                <input type="text" id="searchInput" class="form-control ps-4"
                       placeholder="    Pizzas, hamburguesas, bebidas…"
                       value="<?= limpiarTexto($buscar) ?>"
                       autocomplete="off">
            </div>
        </div>
        <!-- Pills de categoría -->
       <!--  <div>
            <label class="form-label">Categoría</label>
            <div class="cat-pills" id="catPills">
                <button class="cat-pill <?= $categoria === 0 ? 'active' : '' ?>" data-cat="0">Todas</button>
                <?php foreach ($categorias as $cat): ?>
                    <button class="cat-pill <?= $categoria === (int)$cat['id_categoria'] ? 'active' : '' ?>"
                            data-cat="<?= (int)$cat['id_categoria'] ?>">
                        <?= limpiarTexto($cat['nombre_categoria']) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div> -->
    </div>

    <!-- Contador -->
    <p class="text-muted small mb-4" id="resultCount">
        <?= count($productos) ?> producto<?= count($productos) !== 1 ? 's' : '' ?> encontrado<?= count($productos) !== 1 ? 's' : '' ?>
    </p>

    <!-- Grid de productos -->
    <?php if (empty($productos)): ?>
        <div class="alert border text-center py-4" id="noResults">No se encontraron productos.</div>
        <div class="row g-4" id="productGrid"></div>
    <?php else: ?>
        <div class="alert border text-center py-4 d-none" id="noResults">No se encontraron productos.</div>
        <div class="row g-4" id="productGrid">
            <?php foreach ($productos as $producto): ?>
                <?php
                    $precioFinal = !empty($producto['precio_oferta']) ? $producto['precio_oferta'] : $producto['precio'];
                    $imagen = assetPath($producto['imagen'] ?? '', 'assets/img/logo/placeholder.svg');
                ?>
                <div class="col-sm-6 col-lg-3 product-item"
                     data-name="<?= strtolower(limpiarTexto($producto['nombre_producto'])) ?>"
                     data-desc="<?= strtolower(limpiarTexto($producto['descripcion'] ?? '')) ?>"
                     data-cat="<?= (int)$producto['id_categoria'] ?>">
                    <div class="product-card h-100">
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
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<script>
(function () {
    const items    = document.querySelectorAll('.product-item');
    const search   = document.getElementById('searchInput');
    const pills    = document.querySelectorAll('.cat-pill');
    const noRes    = document.getElementById('noResults');
    const counter  = document.getElementById('resultCount');
    let activeCat  = <?= $categoria ?>;

    function filter() {
        const q = search.value.toLowerCase().trim();
        let visible = 0;
        items.forEach(item => {
            const matchCat  = activeCat === 0 || Number(item.dataset.cat) === activeCat;
            const matchText = q === '' || item.dataset.name.includes(q) || item.dataset.desc.includes(q);
            const show = matchCat && matchText;
            item.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        noRes.classList.toggle('d-none', visible > 0);
        counter.textContent = visible + ' producto' + (visible !== 1 ? 's' : '') + ' encontrado' + (visible !== 1 ? 's' : '');
    }

    pills.forEach(pill => {
        pill.addEventListener('click', () => {
            pills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            activeCat = Number(pill.dataset.cat);
            filter();
        });
    });

    let debounce;
    search.addEventListener('input', () => {
        clearTimeout(debounce);
        debounce = setTimeout(filter, 220);
    });

    filter();
})();
</script>

<?php require_once 'includes/footer.php'; ?>
