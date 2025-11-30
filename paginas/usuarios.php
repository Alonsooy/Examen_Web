<?php
session_start();
require '../logica/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
    header("Location: panel.php");
    exit;
}

$stmt = $conn->query("SELECT * FROM usuarios");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion de Usuarios</title>
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
            <h2 class="text-primary fw-bold">Gestion de Usuarios</h2>
            <a href="panel.php" class="btn btn-outline-secondary">Volver al Panel</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="mb-3 text-end">
                    <a href="registro_usuario.php" class="btn btn-success">
                        <i class="bi bi-person-plus-fill"></i> Nuevo Usuario
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usu): ?>
                            <tr>
                                <td><?php echo $usu['id']; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($usu['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($usu['email']); ?></td>
                                <td>
                                    <?php if($usu['rol'] == 'admin'): ?>
                                        <span class="badge bg-primary text-white border">ADMIN</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-success border">EMPLEADO</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="editar_usuario.php?id=<?php echo $usu['id']; ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i>Editar</a>
                                    
                                    <?php if($usu['id'] != $_SESSION['user_id']): ?>
                                        <a href="../logica/eliminar_usuario.php?id=<?php echo $usu['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar usuario?');"><i class="bi bi-trash"></i>Eliminar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>