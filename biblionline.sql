--Crear base de datos BibliOnline
DROP DATABASE IF EXISTS BibliOnline;
CREATE DATABASE BibliOnline;
USE BibliOnline;

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

-- Tabla: Historial (Bitácora)
CREATE TABLE Historial (
    id_historial INT PRIMARY KEY auto_increment,
    mensaje varchar(1000)
);

/* ================================================
   TRIGGERS PARA BITÁCORA
   ================================================ */

-- Trigger para tabla Administrador
DROP TRIGGER IF EXISTS registroBitacoraAdministrador;
DELIMITER //
CREATE TRIGGER registroBitacoraAdministrador AFTER INSERT ON Administrador FOR EACH ROW
BEGIN
    INSERT INTO Historial 
    VALUES (0, CONCAT_WS(' ', 'Administrador Registrado:', NEW.nombre, NEW.apellidos, 'Correo:', NEW.correo, 'Cargo:', NEW.cargo, 'Usuario:', USER(), 'Fecha:', NOW()));
END;
//
DELIMITER ;

-- Trigger para tabla Docente
DROP TRIGGER IF EXISTS registroBitacoraDocente;
DELIMITER //
CREATE TRIGGER registroBitacoraDocente AFTER INSERT ON Docente FOR EACH ROW
BEGIN
    INSERT INTO Historial 
    VALUES (0, CONCAT_WS(' ', 'Docente Registrado:', NEW.nombre, NEW.apellidos, 'Correo:', NEW.correo, 'Especialidad:', NEW.especialidad, 'Usuario:', USER(), 'Fecha:', NOW()));
END;
//
DELIMITER ;

-- Trigger para tabla Alumno
DROP TRIGGER IF EXISTS registroBitacoraAlumno;
DELIMITER //
CREATE TRIGGER registroBitacoraAlumno AFTER INSERT ON Alumno FOR EACH ROW
BEGIN
    INSERT INTO Historial 
    VALUES (0, CONCAT_WS(' ', 'Alumno Registrado:', NEW.nombre, NEW.apellidos, 'Matrícula:', NEW.matricula, 'Correo:', NEW.correo, 'Usuario:', USER(), 'Fecha:', NOW()));
END;
//
DELIMITER ;