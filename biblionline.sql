--Crear base de datos BiblioOnline
DROP DATABASE IF EXISTS BiblioOnline;
CREATE DATABASE BiblioOnline;
USE BiblioOnline;

/* ================================================
   Tablas 
   ================================================ */

-- Tabla: Administrador
CREATE TABLE Administrador (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    apellidos VARCHAR(50),
    telefono VARCHAR(15),
    cargo VARCHAR(50),
    correo VARCHAR(100),
    contrasena VARCHAR(100)
);

-- Tabla: Docente
CREATE TABLE Docente (
    id_docente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    apellidos VARCHAR(50),
    telefono VARCHAR(15),
    especialidad VARCHAR(100),
    correo VARCHAR(100),
    contrasena VARCHAR(100)
);

-- Tabla: Alumno
CREATE TABLE Alumno (
    id_alumno INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    apellidos VARCHAR(50),
    telefono VARCHAR(15),
    matricula VARCHAR(50),
    correo VARCHAR(100),
    contrasena VARCHAR(100)
);

-- Tabla: Historial
CREATE TABLE Historial (
    id_historial INT PRIMARY KEY auto_increment,
    mensaje varchar(1000)
);