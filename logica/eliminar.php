<?php
session_start();
require 'db.php'; 
if (!isset($_SESSION['user_id'])) {
    header("Location: ../paginas/login.php");
    exit;
}

if (isset($_GET['id'])) {
    $stmt = $conn->prepare("DELETE FROM medicamentos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

header("Location: ../paginas/panel.php?msg=eliminado");
exit;
?>