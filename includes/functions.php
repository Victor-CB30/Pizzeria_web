<?php

function limpiarTexto($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

function formatoGs($monto) {
    return 'Gs. ' . number_format((float)$monto, 0, ',', '.');
}

function formatearPrecio($precio) {
    return formatoGs($precio);
}

function assetPath($path, $default = 'assets/img/logo/placeholder.svg') {
    $path = trim((string)$path);
    if ($path === '') return $default;
    if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
    return $path;
}

function imagenProducto($imagen) {
    return assetPath($imagen, 'assets/img/logo/placeholder.svg');
}

function obtenerEmpresa($pdo) {
    $default = [
        'nombre_empresa' => APP_NAME,
        'nombre' => APP_NAME,
        'slogan' => 'Las mejores pizzas y hamburguesas para compartir.',
        'telefono' => WHATSAPP_NUMBER,
        'direccion' => 'Ciudad del Este, Paraguay',
        'horario' => 'Lunes a domingo de 18:00 a 23:30',
        'correo' => 'contacto@pizzeria.local',
        'logo' => DEFAULT_LOGO,
        'imagen_portada' => DEFAULT_HERO
    ];

    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'informacion_empresa'");
        if (!$stmt->fetch()) return $default;

        $stmt = $pdo->query("SELECT * FROM informacion_empresa WHERE estado = 1 ORDER BY id_empresa ASC LIMIT 1");
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$empresa) return $default;

        $empresa['nombre'] = $empresa['nombre_empresa'] ?? $default['nombre_empresa'];
        return array_merge($default, $empresa);
    } catch (Exception $e) {
        return $default;
    }
}

function obtenerCategorias($pdo) {
    $sql = "SELECT * FROM categorias WHERE estado = 1 ORDER BY nombre_categoria ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerProductos($pdo, $categoria = 0, $buscar = '') {
    $sql = "
        SELECT p.*, c.nombre_categoria
        FROM productos p
        INNER JOIN categorias c ON c.id_categoria = p.id_categoria
        WHERE p.estado = 1
    ";
    $params = [];

    if (!empty($categoria) && is_numeric($categoria)) {
        $sql .= " AND p.id_categoria = :categoria";
        $params[':categoria'] = (int)$categoria;
    }

    if (trim((string)$buscar) !== '') {
        $sql .= " AND (
            p.nombre_producto LIKE :buscar
            OR p.descripcion LIKE :buscar
            OR c.nombre_categoria LIKE :buscar
        )";
        $params[':buscar'] = '%' . trim($buscar) . '%';
    }

    $sql .= " ORDER BY p.destacado DESC, p.nombre_producto ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerDestacados($pdo, $limite = 8) {
    $sql = "
        SELECT p.*, c.nombre_categoria
        FROM productos p
        INNER JOIN categorias c ON c.id_categoria = p.id_categoria
        WHERE p.estado = 1 AND p.destacado = 1
        ORDER BY p.id_producto DESC
        LIMIT :limite
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerProductoPorId($pdo, $id) {
    $sql = "
        SELECT p.*, c.nombre_categoria
        FROM productos p
        INNER JOIN categorias c ON c.id_categoria = p.id_categoria
        WHERE p.id_producto = :id AND p.estado = 1
        LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => (int)$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function obtenerMetodosPago($pdo) {
    $stmt = $pdo->query("SELECT * FROM metodos_pago WHERE estado = 1 ORDER BY nombre_metodo ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerEstadosPedido($pdo) {
    $stmt = $pdo->query("SELECT * FROM estados_pedido ORDER BY orden_estado ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function verificarAdmin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

function adminActual() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return $_SESSION['admin_nombre'] ?? 'Administrador';
}
