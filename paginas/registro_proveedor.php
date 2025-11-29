<?php
session_start();
require '../logica/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);

    if (empty($nombre)) {
        $mensaje = "<div class='alert alert-danger'>El nombre es obligatorio.</div>";
    } else {
        try {
            $sql = "INSERT INTO proveedores (nombre, telefono, direccion) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $telefono, $direccion]);
            $mensaje = "<div class='alert alert-success'>Proveedor registrado con exito.</div>";
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
    <title>Nuevo Proveedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Registrar Proveedor</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php echo $mensaje; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nombre Empresa</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Telefono</label>
                                <input type="text" name="telefono" class="form-control">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Direccion</label>
                                <textarea name="direccion" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-info text-white btn-lg">Guardar</button>
                                <a href="proveedores.php" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>