<?php
session_start();
require '../logica/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
    header("Location: login.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $clave = $_POST['clave'];
    $rol = $_POST['rol'];

    if (empty($nombre) || empty($email) || empty($clave)) {
        $mensaje = "<div class='alert alert-danger'>Todos los campos son obligatorios.</div>";
    } else {
        $clave_hash = password_hash($clave, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO usuarios (nombre, email, clave, rol) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $email, $clave_hash, $rol]);
            $mensaje = "<div class='alert alert-success'>Usuario registrado correctamente.</div>";
        } catch(PDOException $e) {
            $mensaje = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuevo Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Registrar Usuario</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php echo $mensaje; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" name="nombre" class="form-control" placeholder="Ej. Javier Pérez" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Correo Electronico</label>
                                <input type="email" name="email" class="form-control" placeholder="Ej. Correo@gmail.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="clave" class="form-control" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Rol</label>
                                <select name="rol" class="form-select">
                                    <option value="empleado">Empleado</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">Guardar Usuario</button>
                                <a href="usuarios.php" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>