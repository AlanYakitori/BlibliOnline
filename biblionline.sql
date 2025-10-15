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
    contrasena VARCHAR(255),
    aceptado BOOLEAN DEFAULT false,
    gusto BOOLEAN DEFAULT false
);

-- Tabla: Docente
CREATE TABLE Docente (
    id_docente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    apellidos VARCHAR(50),
    telefono VARCHAR(15),
    especialidad VARCHAR(100),
    correo VARCHAR(100),
    contrasena VARCHAR(255),
    aceptado BOOLEAN DEFAULT false,
    gusto BOOLEAN DEFAULT false
);

-- Tabla: Alumno
CREATE TABLE Alumno (
    id_alumno INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    apellidos VARCHAR(50),
    telefono VARCHAR(15),
    matricula VARCHAR(50),
    correo VARCHAR(100),
    contrasena VARCHAR(255),
    gusto BOOLEAN DEFAULT false
);

-- Tabla: Categoria
CREATE TABLE Categoria (
    id_categoria INT PRIMARY KEY auto_increment,
    nombre VARCHAR(50)
);

-- Tabla: CategoriasUsuario
CREATE TABLE CategoriasUsuario (
    id_categoria_usuario INT PRIMARY KEY auto_increment,
    id_admin INT DEFAULT NULL,
    id_docente INT DEFAULT NULL,
    id_alumno INT DEFAULT NULL,
    id_categoria INT,
    FOREIGN KEY (id_admin) REFERENCES Administrador(id_admin) ON DELETE CASCADE,
    FOREIGN KEY (id_docente) REFERENCES Docente(id_docente) ON DELETE CASCADE,
    FOREIGN KEY (id_alumno) REFERENCES Alumno(id_alumno) ON DELETE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES Categoria(id_categoria) ON DELETE CASCADE
);

-- Tabla: Recurso
CREATE TABLE Recurso (
    id_recurso INT PRIMARY KEY auto_increment,
    titulo VARCHAR(100),
    descripcion VARCHAR(500),
    archivo_url VARCHAR(10000),
    calificacion FLOAT DEFAULT 0,
    id_categoria INT,
    id_admin INT DEFAULT NULL,
    id_docente INT DEFAULT NULL,
    id_alumno INT DEFAULT NULL,
    FOREIGN KEY (id_admin) REFERENCES Administrador(id_admin) ON DELETE SET NULL,
    FOREIGN KEY (id_docente) REFERENCES Docente(id_docente) ON DELETE SET NULL,
    FOREIGN KEY (id_alumno) REFERENCES Alumno(id_alumno) ON DELETE SET NULL,
    FOREIGN KEY (id_categoria) REFERENCES Categoria(id_categoria) ON DELETE SET NULL
);

-- Tabla: ListasFavoritos
CREATE TABLE ListasFavoritos (
    id_lista_favoritos INT PRIMARY KEY auto_increment,
    id_admin INT DEFAULT NULL,
    id_docente INT DEFAULT NULL,
    id_alumno INT DEFAULT NULL,
    id_recurso INT,
    FOREIGN KEY (id_admin) REFERENCES Administrador(id_admin) ON DELETE CASCADE,
    FOREIGN KEY (id_docente) REFERENCES Docente(id_docente) ON DELETE CASCADE,
    FOREIGN KEY (id_alumno) REFERENCES Alumno(id_alumno) ON DELETE CASCADE,
    FOREIGN KEY (id_recurso) REFERENCES Recurso(id_recurso) ON DELETE CASCADE
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

/* ================================================
   USUARIOS POR DEFECTO
   ================================================ */

-- Aministradores por defecto
INSERT INTO Administrador VALUES
(0,'Angel Daniel','Lemus Quiroz','5573335833','Programador a cargo','lqao230528@upemor.edu.mx','$2y$10$o5g2A1vE7emJIP9KKbDP7e28br/2G3GW8mx5/Gbe01frvIm4JrK3G',1,1),
(0,'Alan David','Cruztitla Villanueva','7774604299','Programador a cargo','cvao230638@upemor.edu.mx','$2y$10$FHk28foEEzhpm1rvvAuY7OqVzL20/ihYsecAm/RfDH.yPLz68PJ2.',1,0);

-- Docentes por defecto
INSERT INTO Docente VALUES
(0,'Arantxa','Miranda Ramirez','7774204180','Programación Orientada a Objetos','mrao230737@upemor.edu.mx','$2y$10$j.IXSOoYOzoyr1HpAh8or.xzQIhbxJ5BzUykChUutdZ6zsskYPks6',1,0);

-- Alumnos por defecto
INSERT INTO Alumno VALUES
(0,'Gerardo','Sanchez Martinez','7775823248','OMEO230484','omeo23084@upemor.edu.mx','$2y$10$f6ZnHoi.Jvcl5Kx.jYuRUeaq3eWIZ5gX7gmM50M6rDcQTiYi8TqYG',0);

-- Categorías por defecto
INSERT INTO Categoria VALUES
(0,'Tecnologia e Informacion'),
(0,'Ciencias Exactas y Naturales'),
(0,'Ciencias Sociales y Humanidades'),
(0,'Ingeniería y Aplicadas'),
(0,'Educación y Pedagogía'),
(0,'Idiomas y Cultura'),
(0,'Administración y Negocios'),
(0,'Energia y Medio Ambiente'),
(0,'Biotecnología y Salud'),
(0,'Manufactura y Producción');

INSERT INTO CategoriasUsuario VALUES
(0,1,NULL,NULL,1),
(0,1,NULL,NULL,2),
(0,1,NULL,NULL,4);