<?php
session_start();

require_once __DIR__ . '/../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = trim($_POST['password'] ?? '');

    if ($usuario === '' || $clave === '') {
        $error = 'Debe ingresar usuario y contraseña.';
    } else {
        $sql = "SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':usuario' => $usuario
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $passwordBD = '';

        if ($user && password_verify($clave, $user['contrasena_hash'])) {
            $_SESSION['admin_id'] = $user['id_usuario'];
            $_SESSION['admin_usuario'] = $user['usuario'];
            $_SESSION['admin_nombre'] = $user['nombre'];
            $_SESSION['admin_rol'] = $user['id_rol'];

            header('Location: index.php');
            exit;
        }

        $loginCorrecto = false;

        if ($user && !empty($passwordBD)) {
            if (password_verify($clave, $passwordBD)) {
                $loginCorrecto = true;
            } elseif ($clave === $passwordBD) {
                $loginCorrecto = true;
            }
        }

        if ($loginCorrecto) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_usuario'] = $user['usuario'];
            $_SESSION['admin_nombre'] = $user['nombre'] ?? $user['usuario'];
            $_SESSION['admin_rol'] = $user['rol_id'] ?? 1;

            header('Location: index.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Admin | Pizzería</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            background: linear-gradient(135deg, #2b1612, #ff6b00);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            background: #fff;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 15px 40px rgba(0,0,0,.25);
        }

        .login-logo {
            font-size: 42px;
            text-align: center;
            margin-bottom: 10px;
        }

        h3 {
            text-align: center;
            margin-bottom: 5px;
        }

        p {
            text-align: center;
            color: #666;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background: linear-gradient(90deg, #e52d27, #ff6b00);
            border: none;
            color: #fff;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
        }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 10px;
            border-radius: 8px;
            margin-top: 15px;
            text-align: center;
        }

        .volver {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #333;
        }
    </style>
</head>
<body>

<div class="login-box">
    <div class="login-logo">🍕</div>

    <h3>Panel Administrador</h3>
    <p>Ingresá tus datos para continuar</p>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Usuario</label>
        <input type="text" name="usuario" required>

        <label>Contraseña</label>
        <input type="password" name="password" required>

        <button type="submit">Iniciar sesión</button>
    </form>

    <a href="../index.php" class="volver">Volver al sitio</a>
</div>

</body>
</html>