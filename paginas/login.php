<?php
session_start();
require '../logica/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: panel.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $clave = trim($_POST['clave']);

    if (empty($email) || empty($clave)) {
        $mensaje = "Llene todos los campos.";
    } else {
        $stmt = $conn->prepare("SELECT id, nombre, clave, rol FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($clave, $usuario['clave'])) {
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_nombre'] = $usuario['nombre'];
            $_SESSION['user_rol'] = $usuario['rol'];
            session_regenerate_id(true); 
            header("Location: panel.php");
            exit;
        } else {
            $mensaje = "Credenciales incorrectas.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Salud Total</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4 text-primary fw-bold">Salud Total</h3>
                        <?php if(!empty($mensaje)): ?>
                            <div class="alert alert-danger text-center"><?php echo $mensaje; ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Correo</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="clave" class="form-control" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Ingresar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>