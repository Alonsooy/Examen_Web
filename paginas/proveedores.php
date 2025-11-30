<?php
session_start();
require '../logica/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $conn->query("SELECT * FROM proveedores ORDER BY id DESC");
$proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proveedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="panel.php">Salud Total</a>
            <div class="d-flex align-items-center text-white">
                <small class="me-3 d-none d-md-block">
                    <?php echo htmlspecialchars($_SESSION['user_nombre']); ?>
                </small>
                <a href="../logica/logout.php" class="btn btn-outline-light btn-sm">Salir</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary fw-bold">Proveedores</h2>
            <a href="panel.php" class="btn btn-outline-secondary">Volver al Panel</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="mb-3 text-end">
                    <a href="registro_proveedor.php" class="btn btn-success">
                        <i class="bi bi-plus-lg"></i> Nuevo Proveedor
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Telefono</th>
                                <th>Direccion</th>
                                <th class="text-end">Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($proveedores) > 0): ?>
                                <?php foreach ($proveedores as $prov): ?>
                                <tr>
                                    <td><?php echo $prov['id']; ?></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($prov['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($prov['telefono']); ?></td>
                                    <td class="small text-muted"><?php echo htmlspecialchars($prov['direccion']); ?></td>
                                    <td class="text-end small"><?php echo $prov['creado_en']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted">No hay proveedores registrados.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>