<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

$empresa      = obtenerEmpresa($pdo);
$categoriasMenu = obtenerCategorias($pdo);
$pageTitle    = $pageTitle ?? $empresa['nombre_empresa'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= limpiarTexto($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top main-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <img src="<?= limpiarTexto(assetPath($empresa['logo'], DEFAULT_LOGO)) ?>"
                 onerror="this.onerror=null;this.src='<?= DEFAULT_LOGO ?>';"
                 alt="Logo" class="brand-logo">
            <span><?= limpiarTexto($empresa['nombre_empresa']) ?></span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="menu.php">Menú</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Categorías</a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <?php foreach ($categoriasMenu as $cat): ?>
                            <li>
                                <a class="dropdown-item" href="menu.php?categoria=<?= (int)$cat['id_categoria'] ?>">
                                    <?= limpiarTexto($cat['nombre_categoria']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
                <li class="nav-item ms-lg-2">
                    <button class="btn btn-order" data-bs-toggle="offcanvas" data-bs-target="#cartCanvas">
                        <i class="bi bi-cart3"></i> Carrito
                        <span id="cartCount" class="badge bg-white text-dark ms-1">0</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main>
