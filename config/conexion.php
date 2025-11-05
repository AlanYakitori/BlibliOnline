<?php
// ===================================
// CONEXIÓN A LA BASE DE DATOS MYSQL
// ===================================

// Datos de conexión (XAMPP por defecto)
$servidor = 'localhost';
$usuario = 'root';
$contraseña = '';  // Sin contraseña en XAMPP
$base_de_datos = 'biblionline';

// Intentar conectar a MySQL
try {
    $conexion = new PDO("mysql:host=$servidor;dbname=$base_de_datos", $usuario, $contraseña);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $error) {
    die("Error al conectar: " . $error->getMessage());
}
?>