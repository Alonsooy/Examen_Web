<?php
session_start();
require '../logica/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$where = "";
$params = [];
$categoria_buscada = "";

if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
    $categoria_buscada = $_GET['categoria'];
    $where = "WHERE m.categoria LIKE ?";
    $params[] = "%$categoria_buscada%";
}

$sql = "SELECT m.id, m.nombre, m.categoria, m.cantidad, m.precio, p.nombre AS proveedor 
        FROM medicamentos m 
        LEFT JOIN proveedores p ON m.proveedor_id = p.id 
        $where ORDER BY m.id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Inventario</title>
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

    <div class="container">
        <?php if (isset($_GET['eliminado'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>¡Eliminado!</strong> El medicamento fue borrado correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-5">
                        <form method="GET" class="d-flex gap-2">
                            <input type="text" name="categoria" class="form-control" placeholder="Buscar categoría..." value="<?php echo htmlspecialchars($categoria_buscada); ?>">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                    <div class="col-12 col-md-7 text-md-end">
                        <?php if($_SESSION['user_rol'] == 'admin'): ?>
                            <a href="usuarios.php" class="btn btn-secondary me-2"><i class="bi bi-people"></i> Usuarios</a>
                        <?php endif; ?>
                        <a href="proveedores.php" class="btn btn-info text-white me-2"><i class="bi bi-truck"></i> Proveedores</a>
                        <a href="registro.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Nuevo</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Medicamento</th>
                            <th>Categoría</th>
                            <th>Proveedor</th>
                            <th class="text-center">Stock</th>
                            <th>Precio</th>
                            <th class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medicamentos as $med): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-primary"><?php echo htmlspecialchars($med['nombre']); ?></td>
                            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($med['categoria']); ?></span></td>
                            <td class="small"><?php echo htmlspecialchars($med['proveedor'] ?? '-'); ?></td>
                            <td class="text-center">
                                <span class="badge <?php echo ($med['cantidad']<10)?'bg-danger':'bg-success'; ?> rounded-pill">
                                    <?php echo $med['cantidad']; ?>
                                </span>
                            </td>
                            <td>$<?php echo number_format($med['precio'], 2); ?></td>
                            <td class="text-end pe-3">

                                <a href="editar.php?id=<?php echo $med['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>Editar
                                </a>

                                <button 
                                    class="btn btn-danger btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#confirmarEliminar<?= $med['id'] ?>">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>

                                <div class="modal fade" id="confirmarEliminar<?= $med['id'] ?>" tabindex="-1">
                                  <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                      <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Confirmar eliminación</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                      </div>
                                      <div class="modal-body">
                                        ¿Seguro que deseas eliminar el medicamento  
                                        <strong class="text-danger"><?= htmlspecialchars($med['nombre']); ?></strong>?
                                        <br><small>No podrás recuperarlo después.</small>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                                        <a href="../logica/eliminar.php?id=<?= $med['id']; ?>" class="btn btn-danger">
                                            Sí, eliminar
                                        </a>
                                      </div>
                                    </div>
                                  </div>
                                </div>

                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
