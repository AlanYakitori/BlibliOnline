-- Crear base de datos BibliOnline
DROP DATABASE IF EXISTS BibliOnline;
CREATE DATABASE BibliOnline;
USE BibliOnline;

/* ================================================
   Tablas 
   ================================================ */

-- Tabla: Usuarios
CREATE TABLE Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    apellidos VARCHAR(50),
    tipoUsuario VARCHAR(15),
    telefono VARCHAR(15),
    dato VARCHAR(100),
    correo VARCHAR(100),
    contrasena VARCHAR(255),
    aceptado BOOLEAN DEFAULT false,
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
    id_usuario INT,
    id_categoria INT,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES Categoria(id_categoria) ON DELETE CASCADE
);

-- Tabla: Recurso
CREATE TABLE Recurso (
    id_recurso INT PRIMARY KEY auto_increment,
    titulo VARCHAR(100),
    descripcion TEXT,
    archivo_url TEXT,
    imagen_url TEXT DEFAULT 'https://media.istockphoto.com/id/1147544807/es/vector/no-imagen-en-miniatura-gr%C3%A1fico-vectorial.jpg?s=612x612&w=0&k=20&c=Bb7KlSXJXh3oSDlyFjIaCiB9llfXsgS7mHFZs6qUgVk=',
    calificacion FLOAT DEFAULT 0,
    aprobado BOOLEAN DEFAULT false,
    id_categoria INT,
    id_usuario INT,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES Categoria(id_categoria) ON DELETE CASCADE
);

-- Tabla: ListasFavoritos
CREATE TABLE ListasFavoritos (
    id_lista_favoritos INT PRIMARY KEY auto_increment,
    id_usuario INT DEFAULT NULL,
    id_recurso INT,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_recurso) REFERENCES Recurso(id_recurso) ON DELETE CASCADE
);

CREATE TABLE Grupos (
    id_grupo INT PRIMARY KEY auto_increment,
    nombre VARCHAR(100),
    clave VARCHAR(100),
    docente INT,
    FOREIGN KEY (docente) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE
);

CREATE TABLE MiembrosGrupo (
    id_miembro_grupo INT PRIMARY KEY auto_increment,
    id_grupo INT,
    id_usuario INT,
    FOREIGN KEY (id_grupo) REFERENCES Grupos(id_grupo) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE
);

CREATE TABLE ListaRechazos (
    id_rechazo INT PRIMARY KEY auto_increment,
    id_usuario INT,
    id_recurso INT,
    motivo TEXT,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
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

-- Trigger para registro de usuario (INSERT)
DROP TRIGGER IF EXISTS registroBitacoraUsuario;
DELIMITER //
CREATE TRIGGER registroBitacoraUsuario AFTER INSERT ON Usuarios FOR EACH ROW
BEGIN
    DECLARE mensaje_log VARCHAR(1000);
    
    IF NEW.tipoUsuario = 'administrador' THEN
        SET mensaje_log = CONCAT_WS(' ', 'Administrador Registrado:', NEW.nombre, NEW.apellidos, 'Correo:', NEW.correo, 'Cargo:', NEW.dato, 'Usuario:', USER(), 'Fecha:', NOW());
    ELSEIF NEW.tipoUsuario = 'docente' THEN
        SET mensaje_log = CONCAT_WS(' ', 'Docente Registrado:', NEW.nombre, NEW.apellidos, 'Correo:', NEW.correo, 'Especialidad:', NEW.dato, 'Usuario:', USER(), 'Fecha:', NOW());
    ELSEIF NEW.tipoUsuario = 'alumno' THEN
        SET mensaje_log = CONCAT_WS(' ', 'Alumno Registrado:', NEW.nombre, NEW.apellidos, 'Matrícula:', NEW.dato, 'Correo:', NEW.correo, 'Usuario:', USER(), 'Fecha:', NOW());
    ELSE
        SET mensaje_log = CONCAT_WS(' ', 'Usuario Registrado:', NEW.nombre, NEW.apellidos, 'Tipo:', NEW.tipoUsuario, 'Correo:', NEW.correo, 'Usuario:', USER(), 'Fecha:', NOW());
    END IF;
    
    INSERT INTO Historial (mensaje) VALUES (mensaje_log);
END;
//
DELIMITER ;

-- Trigger para actualización de usuario (UPDATE)
DROP TRIGGER IF EXISTS actualizacionBitacoraUsuario;
DELIMITER //
CREATE TRIGGER actualizacionBitacoraUsuario AFTER UPDATE ON Usuarios FOR EACH ROW
BEGIN
    DECLARE mensaje_log VARCHAR(1000);
    
    IF NEW.tipoUsuario = 'administrador' THEN
        SET mensaje_log = CONCAT_WS(' ', 'Administrador Actualizado:', NEW.nombre, NEW.apellidos, 'Correo:', NEW.correo, 'Cargo:', NEW.dato, 'Usuario:', USER(), 'Fecha:', NOW());
    ELSEIF NEW.tipoUsuario = 'docente' THEN
        SET mensaje_log = CONCAT_WS(' ', 'Docente Actualizado:', NEW.nombre, NEW.apellidos, 'Correo:', NEW.correo, 'Especialidad:', NEW.dato, 'Usuario:', USER(), 'Fecha:', NOW());
    ELSEIF NEW.tipoUsuario = 'alumno' THEN
        SET mensaje_log = CONCAT_WS(' ', 'Alumno Actualizado:', NEW.nombre, NEW.apellidos, 'Matrícula:', NEW.dato, 'Correo:', NEW.correo, 'Usuario:', USER(), 'Fecha:', NOW());
    ELSE
        SET mensaje_log = CONCAT_WS(' ', 'Usuario Actualizado:', NEW.nombre, NEW.apellidos, 'Tipo:', NEW.tipoUsuario, 'Correo:', NEW.correo, 'Usuario:', USER(), 'Fecha:', NOW());
    END IF;
    
    INSERT INTO Historial (mensaje) VALUES (mensaje_log);
END;
//
DELIMITER ;

-- Trigger para eliminación de usuario (DELETE)
DROP TRIGGER IF EXISTS eliminacionBitacoraUsuario;
DELIMITER //
CREATE TRIGGER eliminacionBitacoraUsuario AFTER DELETE ON Usuarios FOR EACH ROW
BEGIN
    DECLARE mensaje_log VARCHAR(1000);
    
    IF OLD.tipoUsuario = 'administrador' THEN
        SET mensaje_log = CONCAT_WS(' ', 'Administrador Eliminado:', OLD.nombre, OLD.apellidos, 'Correo:', OLD.correo, 'Cargo:', OLD.dato, 'Usuario:', USER(), 'Fecha:', NOW());
    ELSEIF OLD.tipoUsuario = 'docente' THEN
        SET mensaje_log = CONCAT_WS(' ', 'Docente Eliminado:', OLD.nombre, OLD.apellidos, 'Correo:', OLD.correo, 'Especialidad:', OLD.dato, 'Usuario:', USER(), 'Fecha:', NOW());
    ELSEIF OLD.tipoUsuario = 'alumno' THEN
        SET mensaje_log = CONCAT_WS(' ', 'Alumno Eliminado:', OLD.nombre, OLD.apellidos, 'Matrícula:', OLD.dato, 'Correo:', OLD.correo, 'Usuario:', USER(), 'Fecha:', NOW());
    ELSE
        SET mensaje_log = CONCAT_WS(' ', 'Usuario Eliminado:', OLD.nombre, OLD.apellidos, 'Tipo:', OLD.tipoUsuario, 'Correo:', OLD.correo, 'Usuario:', USER(), 'Fecha:', NOW());
    END IF;
    
    INSERT INTO Historial (mensaje) VALUES (mensaje_log);
END;
//
DELIMITER ;

/* ================================================
   USUARIOS POR DEFECTO
   ================================================ */

-- Administradores por defecto
INSERT INTO Usuarios (nombre, apellidos, tipoUsuario, telefono, dato, correo, contrasena, aceptado, gusto, genero, fechaNacimiento) VALUES
('Angel Daniel','Lemus Quiroz','administrador','5573335833','Programador a cargo','lqao230528@upemor.edu.mx','$2y$10$o5g2A1vE7emJIP9KKbDP7e28br/2G3GW8mx5/Gbe01frvIm4JrK3G',1,1,'Masculino','2005-09-24'),
('Alan David','Cruztitla Villanueva','administrador','7774604299','Programador a cargo','cvao230638@upemor.edu.mx','$2y$10$FHk28foEEzhpm1rvvAuY7OqVzL20/ihYsecAm/RfDH.yPLz68PJ2.',1,1,'Masculino','2005-01-20'),
('Jose Mariano','Ocampo Romero','administrador','7776233597','Director de Carrera ITI-IET','orjo230233@upemor.edu.mx','$2y$10$8GnNY1BTLt7c784YOA1xBeZ0qCMHkthKmIKIKbazsDuEZEsO4kAxK',0,0,'Otro','2003-08-19');

-- Docentes por defecto
INSERT INTO Usuarios (nombre, apellidos, tipoUsuario, telefono, dato, correo, contrasena, aceptado, gusto, genero, fechaNacimiento) VALUES
('Arantxa','Miranda Ramirez','docente','7774204180','Programación Orientada a Objetos','mrao230737@upemor.edu.mx','$2y$10$j.IXSOoYOzoyr1HpAh8or.xzQIhbxJ5BzUykChUutdZ6zsskYPks6',1,1,'Femenino','2004-09-10'),
('Manuel Antonio','Arellano Díaz','docente','7771894755','Administración de Base de Datos','admo230179@upemor.edu.mx','$2y$10$IWYNjYoS/ed0c7Rj/WeiX.bfKTWGVdRzCxQZGBfhOINCIb.Y9Rr4W',1,1,'Otro','2005-06-22'),
('Alexis Sebastian','Sanchez Luna','docente','7774805924','Programacion Estructurada','slao230036@upemor.edu.mx','$2y$10$jWrk555ujPb686dVSdtkDer6d11bwJsBxJf5OCkjvUYv/CtfVSJXO',0,0,'Otro','2005-10-27');

-- Alumnos por defecto
INSERT INTO Usuarios (nombre, apellidos, tipoUsuario, telefono, dato, correo, contrasena, aceptado, gusto, genero, fechaNacimiento) VALUES
('Gerardo','Sanchez Martinez','alumno','7775823248','SMGO230581','smgo230581@upemor.edu.mx','$2y$10$ogLEVnUTm3CoLhDXDVZVg.hVorWAm12fvywFOxjCZLYYWIubUahn2',1,1,'Otro','2004-01-20'),
('Edwin Leonardo','Vargas Lopez','alumno','7775396206','VLE0231602','vleo231602@upemor.edu.mx','$2y$10$fGQB87pU4DmYLXjcBG8RVePbVdigNEpHq.n.sfSB3rUy2p.kCA9ny',1,1,'Otro','2003-09-10'),
('Cynthia Jocelyn','Martinez Delgado','alumno','7774306734','MDCO230011','mdco230011@upemor.edu.mx','$2y$10$V4I18TXwABlXuiduDa2ae.WThOlehXYz0VpMRTUS62m2J2o.e0cEi',1,0,'Femenino','2005-06-02');

-- Categorías por defecto
INSERT INTO Categoria (nombre) VALUES
('Tecnologia e Informacion'),
('Ciencias Exactas y Naturales'),
('Ciencias Sociales y Humanidades'),
('Ingeniería y Aplicadas'),
('Educación y Pedagogía'),
('Idiomas y Cultura'),
('Administración y Negocios'),
('Energia y Medio Ambiente'),
('Biotecnología y Salud'),
('Manufactura y Producción');

-- Categorías de usuarios por defecto
INSERT INTO CategoriasUsuario (id_usuario, id_categoria) VALUES
(1,1),
(1,2),
(1,4),
(2,1),
(2,2),
(2,4),
(4,1),
(4,5),
(4,6),
(4,7),
(5,1),
(5,3),
(5,4),
(5,6),
(5,7),
(5,8),
(7,1),
(7,2),
(7,4),
(7,5),
(7,6),
(7,7),
(8,1),
(8,3),
(8,4),
(8,7),
(8,10);

-- Recursos por defecto
INSERT INTO Recurso (titulo, descripcion, archivo_url, calificacion, aprobado, id_categoria, id_usuario) VALUES
('GDB ONLINE','IDE en linea con capacidad de correr diferentes lenguajes de programacion','https://www.onlinegdb.com',0,1,1,1),
('GIT HUB','Controlador de manejo de versiones','https://github.com',0,1,1,2),
('OCEANOFPDF','Pagina web para descargar libros de texto de forma gratuita en formato pdf y epub','https://oceanofpdf.com',0,1,5,4),
('BIB GURU','Generador de citas APA','https://www.bibguru.com/es',0,1,5,5),
('CHATGPT','IA util para documentar','https://chatgpt.com',0,NULL,1,7),
('ILOVEPDF','Convertidor de archivos','https://www.ilovepdf.com/es',0,NULL,5,8);

-- Listas de favoritos por defecto
INSERT INTO ListasFavoritos (id_usuario, id_recurso) VALUES
(1,1),
(1,3),
(1,5),
(1,6),
(2,1),
(2,3),
(2,5),
(2,6),
(4,3),
(4,4),
(4,6),
(5,1),
(5,2),
(5,4),
(5,5),
(7,1),
(7,2),
(7,3),
(7,4),
(7,5),
(7,6),
(8,2),
(8,4),
(8,6);

INSERT INTO Grupos (nombre, clave, docente) VALUES
('ITI-7A','abcdefgh',4),
('ITI-7B','qwertyui',5);

INSERT INTO MiembrosGrupo (id_grupo, id_usuario) VALUES
(1,7),
(1,8),
(2,9);