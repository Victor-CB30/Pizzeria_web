<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
verificarAdmin();
$adminTitle = $adminTitle ?? 'Panel administrativo';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= limpiarTexto($adminTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">Admin Pizzería</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="productos.php">Productos</a></li>
                <li class="nav-item"><a class="nav-link" href="categorias.php">Categorías</a></li>
                <li class="nav-item"><a class="nav-link" href="pedidos.php">Pedidos</a></li>
                <li class="nav-item"><a class="nav-link" href="empresa.php">Empresa</a></li>
                <li class="nav-item"><a class="nav-link" target="_blank" href="../index.php">Ver web</a></li>
            </ul>
            <span class="navbar-text me-3"><?= limpiarTexto(adminActual()) ?></span>
            <a class="btn btn-outline-light btn-sm" href="logout.php">Salir</a>
        </div>
    </div>
</nav>
<main class="container-fluid py-4">
