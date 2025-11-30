<?php
session_start();
require '../logica/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$mensaje = "";
$stmt_prov = $conn->query("SELECT id, nombre FROM proveedores");
$proveedores = $stmt_prov->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmado'])) {
    $nombre = trim($_POST['nombre']);
    $categoria = trim($_POST['categoria']);
    $cantidad = (int) $_POST['cantidad'];
    $precio = (float) $_POST['precio'];
    $proveedor_id = $_POST['proveedor_id'];

    if (empty($nombre) || empty($categoria) || empty($proveedor_id)) {
        $mensaje = "<div class='alert alert-danger'>Todos los campos son obligatorios.</div>";
    } else {
        try {
            $sql = "INSERT INTO medicamentos (nombre, categoria, cantidad, precio, proveedor_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $categoria, $cantidad, $precio, $proveedor_id]);

            header("Location: panel.php?registrado=1");
            exit;

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
    <title>Nuevo Medicamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    :root {
        --main-success: #198754;
        --main-success-hover: #157347;
        --main-gray: #6c757d;
        --main-gray-hover: #5c636a;
    }

    .card-header {
        background: var(--main-success);
        color: white;
    }

    .modal-header {
        background: var(--main-success);
        color: white;
    }

    .btn-success,
    .btn-confirm {
        background-color: var(--main-success) !important;
        border-color: var(--main-success) !important;
        color: white !important;
    }

    .btn-success:hover,
    .btn-confirm:hover {
        background-color: var(--main-success-hover) !important;
        border-color: var(--main-success-hover) !important;
    }

    .btn-cancel {
        background-color: var(--main-gray) !important;
        border-color: var(--main-gray) !important;
        color: white !important;
    }

    .btn-cancel:hover {
        background-color: var(--main-gray-hover) !important;
        border-color: var(--main-gray-hover) !important;
    }

    #nombreMedicamento {
        color: var(--main-success);
    }
</style>

</head>
<body class="bg-light d-flex align-items-center min-vh-100">
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header">
                        <h5 class="mb-0">Nuevo Medicamento</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php echo $mensaje; ?>

                        <form id="formMedicamento" method="POST">
                            <input type="hidden" name="confirmado" value="1">

                            <div class="mb-3">
                                <label class="form-label">Nombre del Medicamento</label>
                                <input type="text" name="nombre" class="form-control" placeholder="Ej. Ibuprofeno" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Categoria</label>
                                <input type="text" name="categoria" class="form-control" required>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Stock</label>
                                    <input type="number" name="cantidad" class="form-control" min="1" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Precio ($)</label>
                                    <input type="number" name="precio" class="form-control" step="0.01" min="0" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Proveedor</label>
                                <select name="proveedor_id" class="form-select" required>
                                    <option value="">Seleccione una opción...</option>
                                    <?php foreach($proveedores as $prov): ?>
                                        <option value="<?php echo $prov['id']; ?>">
                                            <?php echo htmlspecialchars($prov['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="button" id="btnConfirmar" class="btn btn-success btn-lg">Guardar Registro</button>
                                <a href="panel.php" class="btn btn-cancel btn-lg">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Confirmar Registro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <h5 class="text-center">
            ¿Deseas registrar el medicamento:  
            <span id="nombreMedicamento" class="fw-bold"></span> ?
        </h5>
      </div>

      <div class="modal-footer d-flex justify-content-between">
        <button class="btn btn-cancel" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-confirm" id="btnEnviar">Registrar</button>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
const btnConfirmar = document.getElementById("btnConfirmar");
const btnEnviar = document.getElementById("btnEnviar");
const form = document.getElementById("formMedicamento");

btnConfirmar.addEventListener('click', function() {
    let nombre = document.querySelector('input[name="nombre"]').value;

    if (nombre.trim() === "") {
        alert("Debes escribir un nombre de medicamento.");
        return;
    }

    document.getElementById("nombreMedicamento").textContent = nombre;

    let modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
});

btnEnviar.addEventListener('click', function() {
    form.submit();
});
</script>

</body>
</html>
