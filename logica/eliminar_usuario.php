<?php
session_start();
require 'db.php';


if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    if ($_GET['id'] == $_SESSION['user_id']) {
        die("No puedes eliminar tu propia cuenta.");
    }

    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    header("Location: usuarios.php");
}
?>