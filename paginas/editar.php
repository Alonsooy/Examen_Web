<?php
session_start();
require '../logica/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: panel.php");
    exit;
}

$id = $_GET['id'];
$mensaje = "";

$stmt = $conn->prepare("SELECT * FROM medicamentos WHERE id = ?");
$stmt->execute([$id]);
$medicamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medicamento) {
    header("Location: panel.php");
    exit;
}

$stmt_prov = $conn->query("SELECT id, nombre FROM proveedores");
$proveedores = $stmt_prov->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $categoria = trim($_POST['categoria']);
    $cantidad = (int) $_POST['cantidad'];
    $precio = (float) $_POST['precio'];
    $proveedor_id = $_POST['proveedor_id'];

    if (empty($nombre) || empty($categoria) || empty($proveedor_id)) {
        $mensaje = "<div class='alert alert-danger'>Todos los campos son obligatorios.</div>";
    } else {
        try {
            $sql = "UPDATE medicamentos SET nombre=?, categoria=?, cantidad=?, precio=?, proveedor_id=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $categoria, $cantidad, $precio, $proveedor_id, $id]);
            
            $medicamento['nombre'] = $nombre;
            $medicamento['categoria'] = $categoria;
            $medicamento['cantidad'] = $cantidad;
            $medicamento['precio'] = $precio;
            $medicamento['proveedor_id'] = $proveedor_id;

            $mensaje = "<div class='alert alert-success'>Registro actualizado correctamente.</div>";
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
    <title>Editar Medicamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --main-success: #198754;
            --main-success-hover: #157347;
        }

        .btn-success {
            background-color: var(--main-success) !important;
            border-color: var(--main-success) !important;
            color: white !important;
        }

        .btn-success:hover {
            background-color: var(--main-success-hover) !important;
            border-color: var(--main-success-hover) !important;
        }

        #nombreEditar {
            color: #ffbe0b;
        }
    </style>
</head>
<body class="bg-light d-flex align-items-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Editar Medicamento</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php echo $mensaje; ?>
                        <?php if (strpos($mensaje, 'Registro actualizado correctamente') !== false): ?>
                        <script>
                            window.location.href = "panel.php";
                        </script>
                        <?php endif; ?>
                        <form method="POST" id="formEditar">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($medicamento['nombre']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Categoria</label>
                                <input type="text" name="categoria" class="form-control" value="<?php echo htmlspecialchars($medicamento['categoria']); ?>" required>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Stock</label>
                                    <input type="number" name="cantidad" class="form-control" value="<?php echo $medicamento['cantidad']; ?>" min="0" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Precio</label>
                                    <input type="number" name="precio" class="form-control" value="<?php echo $medicamento['precio']; ?>" step="0.01" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Proveedor</label>
                                <select name="proveedor_id" class="form-select" required>
                                    <?php foreach($proveedores as $prov): ?>
                                        <option value="<?php echo $prov['id']; ?>" <?php if($prov['id'] == $medicamento['proveedor_id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($prov['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning btn-lg text-dark fw-bold">Guardar Cambios</button>
                                <a href="panel.php" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="confirmEditModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title fw-bold">Confirmar Cambios</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-center">
        <h5 class="fw-semibold mb-3">
            ¿Deseas guardar los cambios del medicamento?
        </h5>

        <span id="nombreEditar" class="fw-bold fs-4"></span>
      </div>

      <div class="modal-footer d-flex justify-content-between">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-warning text-dark fw-bold" id="btnConfirmarEdicion">Guardar Cambios</button>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
const form = document.getElementById("formEditar");
let modalConfirm = new bootstrap.Modal(document.getElementById("confirmEditModal"));
let btnConfirmar = document.getElementById("btnConfirmarEdicion");

form.addEventListener("submit", function(e) {
    e.preventDefault();

    let cantidad = parseFloat(document.querySelector('input[name="cantidad"]').value);
    let precio = parseFloat(document.querySelector('input[name="precio"]').value);
    let nombre = document.querySelector('input[name="nombre"]').value;

    if (cantidad < 0 || precio < 0) {
        alert("¡Atención! El stock y el precio no pueden ser números negativos.");
        return;
    }

    document.getElementById("nombreEditar").textContent = nombre;

    modalConfirm.show();
});

btnConfirmar.addEventListener("click", function() {
    form.submit();
});
</script>

</body>
</html>
