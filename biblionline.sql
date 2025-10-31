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
    gusto BOOLEAN DEFAULT false,
    genero VARCHAR(15),
    fechaNacimiento DATE
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
    gusto BOOLEAN DEFAULT false,
    genero VARCHAR(15),
    fechaNacimiento DATE
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
    gusto BOOLEAN DEFAULT false,
    genero VARCHAR(15),
    fechaNacimiento DATE
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

-- Triggers para tabla Administrador
DROP TRIGGER IF EXISTS registroBitacoraAdministrador;
DELIMITER //
CREATE TRIGGER registroBitacoraAdministrador AFTER INSERT ON Administrador FOR EACH ROW
BEGIN
    INSERT INTO Historial 
    VALUES (0, CONCAT_WS(' ', 'Administrador Registrado:', NEW.nombre, NEW.apellidos, 'Correo:', NEW.correo, 'Cargo:', NEW.cargo, 'Usuario:', USER(), 'Fecha:', NOW()));
END;
//
DELIMITER ;

DROP TRIGGER IF EXISTS actualizacionBitacoraAdministrador;
DELIMITER //
CREATE TRIGGER actualizacionBitacoraAdministrador AFTER UPDATE ON Administrador FOR EACH ROW
BEGIN
    INSERT INTO Historial 
    VALUES (0, CONCAT_WS(' ', 'Administrador Actualizado:', NEW.nombre, NEW.apellidos, 'Correo:', NEW.correo, 'Cargo:', NEW.cargo, 'Usuario:', USER(), 'Fecha:', NOW()));
END;
//
DELIMITER ;

DROP TRIGGER IF EXISTS eliminacionBitacoraAdministrador;
DELIMITER //
CREATE TRIGGER eliminacionBitacoraAdministrador AFTER DELETE ON Administrador FOR EACH ROW
BEGIN
    INSERT INTO Historial 
    VALUES (0, CONCAT_WS(' ', 'Administrador Eliminado:', OLD.nombre, OLD.apellidos, 'Correo:', OLD.correo, 'Cargo:', OLD.cargo, 'Usuario:', USER(), 'Fecha:', NOW()));
END;
//
DELIMITER ;

-- Triggers para tabla Docente
DROP TRIGGER IF EXISTS registroBitacoraDocente;
DELIMITER //
CREATE TRIGGER registroBitacoraDocente AFTER INSERT ON Docente FOR EACH ROW
BEGIN
    INSERT INTO Historial 
    VALUES (0, CONCAT_WS(' ', 'Docente Registrado:', NEW.nombre, NEW.apellidos, 'Correo:', NEW.correo, 'Especialidad:', NEW.especialidad, 'Usuario:', USER(), 'Fecha:', NOW()));
END;
//
DELIMITER ;

DROP TRIGGER IF EXISTS actualizacionBitacoraDocente;
DELIMITER //
CREATE TRIGGER actualizacionBitacoraDocente AFTER UPDATE ON Docente FOR EACH ROW
BEGIN
    INSERT INTO Historial 
    VALUES (0, CONCAT_WS(' ', 'Docente Actualizado:', NEW.nombre, NEW.apellidos, 'Correo:', NEW.correo, 'Especialidad:', NEW.especialidad, 'Usuario:', USER(), 'Fecha:', NOW()));
END;
//
DELIMITER ;

DROP TRIGGER IF EXISTS eliminacionBitacoraDocente;
DELIMITER //
CREATE TRIGGER eliminacionBitacoraDocente AFTER DELETE ON Docente FOR EACH ROW
BEGIN
    INSERT INTO Historial 
    VALUES (0, CONCAT_WS(' ', 'Docente Eliminado:', OLD.nombre, OLD.apellidos, 'Correo:', OLD.correo, 'Especialidad:', OLD.especialidad, 'Usuario:', USER(), 'Fecha:', NOW()));
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

DROP TRIGGER IF EXISTS actualizacionBitacoraAlumno;
DELIMITER //
CREATE TRIGGER actualizacionBitacoraAlumno AFTER UPDATE ON Alumno FOR EACH ROW
BEGIN
    INSERT INTO Historial 
    VALUES (0, CONCAT_WS(' ', 'Alumno Actualizado:', NEW.nombre, NEW.apellidos, 'Matrícula:', NEW.matricula, 'Correo:', NEW.correo, 'Usuario:', USER(), 'Fecha:', NOW()));
END;
//
DELIMITER ;

DROP TRIGGER IF EXISTS eliminacionBitacoraAlumno;
DELIMITER //
CREATE TRIGGER eliminacionBitacoraAlumno AFTER DELETE ON Alumno FOR EACH ROW
BEGIN
    INSERT INTO Historial 
    VALUES (0, CONCAT_WS(' ', 'Alumno Actualizado:', OLD.nombre, OLD.apellidos, 'Matrícula:', OLD.matricula, 'Correo:', OLD.correo, 'Usuario:', USER(), 'Fecha:', NOW()));
END;
//
DELIMITER ;

/* ================================================
   USUARIOS POR DEFECTO
   ================================================ */

-- Aministradores por defecto
INSERT INTO Administrador VALUES
(0,'Angel Daniel','Lemus Quiroz','5573335833','Programador a cargo','lqao230528@upemor.edu.mx','$2y$10$o5g2A1vE7emJIP9KKbDP7e28br/2G3GW8mx5/Gbe01frvIm4JrK3G',1,1,'Masculino','2005-09-24'),
(0,'Alan David','Cruztitla Villanueva','7774604299','Programador a cargo','cvao230638@upemor.edu.mx','$2y$10$FHk28foEEzhpm1rvvAuY7OqVzL20/ihYsecAm/RfDH.yPLz68PJ2.',1,1,'Masculino','2005-01-20'),
(0,'Jose Mariano','Ocampo Romero','7776233597','Director de Carrera ITI-IET','orjo230233@upemor.edu.mx','$2y$10$8GnNY1BTLt7c784YOA1xBeZ0qCMHkthKmIKIKbazsDuEZEsO4kAxK',0,0,'Otro','2003-08-19');

-- Docentes por defecto
INSERT INTO Docente VALUES
(0,'Arantxa','Miranda Ramirez','7774204180','Programación Orientada a Objetos','mrao230737@upemor.edu.mx','$2y$10$j.IXSOoYOzoyr1HpAh8or.xzQIhbxJ5BzUykChUutdZ6zsskYPks6',1,1,'Femenino','2004-09-10'),
(0,'Manuel Antonio','Arellano Díaz','7771894755','Administración de Base de Datos','admo230179@upemor.edu.mx','$2y$10$IWYNjYoS/ed0c7Rj/WeiX.bfKTWGVdRzCxQZGBfhOINCIb.Y9Rr4W',1,1,'Otro','2005-06-22'),
(0,'Alexis Sebastian','Sanchez Luna','7774805924','Programacion Estructurada','slao230036@upemor.edu.mx','$2y$10$jWrk555ujPb686dVSdtkDer6d11bwJsBxJf5OCkjvUYv/CtfVSJXO',0,0,'Otro','2005-10-27');

-- Alumnos por defecto
INSERT INTO Alumno VALUES
(0,'Gerardo','Sanchez Martinez','7775823248','SMGO230581','smgo230581@upemor.edu.mx','$2y$10$ogLEVnUTm3CoLhDXDVZVg.hVorWAm12fvywFOxjCZLYYWIubUahn2',1,'Otro','2004-01-20'),
(0,'Edwin Leonardo','Vargas Lopez','7775396206','VLE0231602','vleo231602@upemor.edu.mx','$2y$10$fGQB87pU4DmYLXjcBG8RVePbVdigNEpHq.n.sfSB3rUy2p.kCA9ny',1,'Otro','2003-09-10'),
(0,'Cynthia Jocelyn','Martinez Delgado','7774306734','MDCO230011','mdco230011@upemor.edu.mx','$2y$10$V4I18TXwABlXuiduDa2ae.WThOlehXYz0VpMRTUS62m2J2o.e0cEi',0,'Femenino','2005-06-02');

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
(0,1,NULL,NULL,4),
(0,2,NULL,NULL,1),
(0,2,NULL,NULL,2),
(0,2,NULL,NULL,4),
(0,NULL,1,NULL,1),
(0,NULL,1,NULL,5),
(0,NULL,1,NULL,6),
(0,NULL,1,NULL,7),
(0,NULL,2,NULL,1),
(0,NULL,2,NULL,3),
(0,NULL,2,NULL,4),
(0,NULL,2,NULL,6),
(0,NULL,2,NULL,7),
(0,NULL,2,NULL,8),
(0,NULL,NULL,1,1),
(0,NULL,NULL,1,2),
(0,NULL,NULL,1,4),
(0,NULL,NULL,1,5),
(0,NULL,NULL,1,6),
(0,NULL,NULL,1,7),
(0,NULL,NULL,2,1),
(0,NULL,NULL,2,3),
(0,NULL,NULL,2,4),
(0,NULL,NULL,2,7),
(0,NULL,NULL,2,10);

INSERT INTO Recurso VALUES
(0,'GDB ONLINE','IDE en linea con capacidad de correr diferentes lenguajes de programacion','https://www.onlinegdb.com',5,1,1,NULL,NULL),
(0,'GIT HUB','Controlador de manejo de versiones','https://github.com',5,1,2,NULL,NULL),
(0,'OCEANOFPDF','Pagina web para descargar libros de texto de forma gratuita en formato pdf y epub','https://oceanofpdf.com',5,5,NULL,1,NULL),
(0,'BIB GURU','Generador de citas APA','https://www.bibguru.com/es',5,5,NULL,2,NULL),
(0,'CHATGPT','IA util para documentar','https://chatgpt.com',5,1,NULL,NULL,1),
(0,'ILOVEPDF','Convertidor de archivos','https://www.ilovepdf.com/es',5,5,NULL,NULL,2);

INSERT INTO ListasFavoritos VALUES
(0,1,NULL,NULL,1),
(0,1,NULL,NULL,3),
(0,1,NULL,NULL,5),
(0,1,NULL,NULL,6),
(0,2,NULL,NULL,1),
(0,2,NULL,NULL,3),
(0,2,NULL,NULL,5),
(0,2,NULL,NULL,6),
(0,NULL,1,NULL,3),
(0,NULL,1,NULL,4),
(0,NULL,1,NULL,6),
(0,NULL,2,NULL,1),
(0,NULL,2,NULL,2),
(0,NULL,2,NULL,4),
(0,NULL,2,NULL,5),
(0,NULL,NULL,1,1),
(0,NULL,NULL,1,2),
(0,NULL,NULL,1,3),
(0,NULL,NULL,1,4),
(0,NULL,NULL,1,5),
(0,NULL,NULL,1,6),
(0,NULL,NULL,2,2),
(0,NULL,NULL,2,4),
(0,NULL,NULL,2,6);