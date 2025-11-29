<?php
$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "salud_total";

try {
    $conn = new PDO("mysql:host=$host;dbname=$base_datos;charset=utf8", $usuario, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>