<?php
session_start();
require '../logica/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') { 
    header("Location: login.php"); 
    exit; 
}

$id = $_GET['id'];
$mensaje = "";

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $rol = $_POST['rol'];
    $nueva_clave = $_POST['clave'];

    try {
        if (!empty($nueva_clave)) {
            $clave_hash = password_hash($nueva_clave, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nombre=?, email=?, rol=?, clave=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $email, $rol, $clave_hash, $id]);
        } else {
            $sql = "UPDATE usuarios SET nombre=?, email=?, rol=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $email, $rol, $id]);
        }
        $mensaje = "<div class='alert alert-success'>Usuario actualizado.</div>";
        $usuario['nombre'] = $nombre; 
        $usuario['email'] = $email; 
        $usuario['rol'] = $rol;
    } catch(PDOException $e) {
        $mensaje = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Editar Usuario</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php echo $mensaje; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Contraseña <small class="text-muted">(Dejar vacio para no cambiar)</small></label>
                                <input type="password" name="clave" class="form-control">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Rol</label>
                                <select name="rol" class="form-select">
                                    <option value="empleado" <?php if($usuario['rol']=='empleado') echo 'selected'; ?>>Empleado</option>
                                    <option value="admin" <?php if($usuario['rol']=='admin') echo 'selected'; ?>>Administrador</option>
                                </select>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning btn-lg">Actualizar</button>
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