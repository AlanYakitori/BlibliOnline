<?php
require_once __DIR__ . '/../../config/conexion.php';

class UserModel { 
    private $id;
    private $nombre;
    private $apellidos;
    private $tipoUsuario;
    private $telefono;
    private $dato;
    private $correo;
    private $contrasena;
    private $aceptado;
    private $gusto;
    private $genero;
    private $fechaNacimiento;

    public function __construct() {
        $this->id = '';
        $this->nombre = '';
        $this->apellidos = '';
        $this->tipoUsuario = '';
        $this->telefono = '';
        $this->dato = '';
        $this->correo = '';
        $this->contrasena = '';
        $this->aceptado = '';
        $this->gusto = '';
        $this->genero = '';
        $this->fechaNacimiento = '';
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getApellidos() { return $this->apellidos; }
    public function getTipoUsuario() { return $this->tipoUsuario; }
    public function getTelefono() { return $this->telefono; }
    public function getDato() { return $this->dato; }
    public function getCorreo() { return $this->correo; }
    public function getContrasena() { return $this->contrasena; }
    public function getAceptado() { return $this->aceptado; }
    public function getGusto() { return $this->gusto; }
    public function getGenero() { return $this->genero; }
    public function getFechaNacimiento() { return $this->fechaNacimiento; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setApellidos($apellidos) { $this->apellidos = $apellidos; }
    public function setTipoUsuario($tipoUsuario) { $this->tipoUsuario = $tipoUsuario; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }
    public function setDato($dato) { $this->dato = $dato; }
    public function setCorreo($correo) { $this->correo = $correo; }
    public function setContrasena($contrasena) { $this->contrasena = $contrasena; }
    public function setAceptado($aceptado) { $this->aceptado = $aceptado; }
    public function setGusto($gusto) { $this->gusto = $gusto; }
    public function setGenero($genero) { $this->genero = $genero; }
    public function setFechaNacimiento($fechaNacimiento) { $this->fechaNacimiento = $fechaNacimiento; }

    // Método para verificar si el correo ya existe
    public function correoExiste($conexion) {
        try {
            $sql = "SELECT COUNT(*) FROM Usuarios WHERE correo = :correo";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':correo', $this->correo);
            $stmt->execute();
            
            $count = $stmt->fetchColumn();
            return $count > 0;
            
        } catch (PDOException $e) {
            error_log("Error al verificar correo: " . $e->getMessage());
            return false;
        }
    }

    // Método para registrar un nuevo usuario
    public function registrarUsuario($conexion) {
        $sql = "INSERT INTO Usuarios (nombre, apellidos, tipoUsuario, telefono, dato, correo, contrasena, aceptado, gusto, genero, fechaNacimiento) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(1, $this->nombre);
            $stmt->bindParam(2, $this->apellidos);
            $stmt->bindParam(3, $this->tipoUsuario);
            $stmt->bindParam(4, $this->telefono);
            $stmt->bindParam(5, $this->dato);
            $stmt->bindParam(6, $this->correo);
            $stmt->bindParam(7, $this->contrasena);
            $stmt->bindParam(8, $this->aceptado, PDO::PARAM_BOOL);
            $stmt->bindParam(9, $this->gusto, PDO::PARAM_BOOL);
            $stmt->bindParam(10, $this->genero);
            $stmt->bindParam(11, $this->fechaNacimiento);

            $resultado = $stmt->execute();
            return $resultado;
            
        } catch (PDOException $e) {
            error_log("Error al registrar usuario: " . $e->getMessage());
            return false;
        }
    }

    // Método para validar credenciales y obtener datos del usuario
    public function validarCredenciales($conexion, $correo, $contrasena) {
        try {
            $sql = "SELECT * FROM Usuarios WHERE correo = :correo";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();
            
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Si no se encuentra el usuario
            if (!$usuario) {
                return null;
            }
            
            // Verificar la contraseña
            if (password_verify($contrasena, $usuario['contrasena'])) {
                return $usuario;
            } else {
                // Contraseña incorrecta
                return false;
            }
            
        } catch (PDOException $e) {
            error_log("Error al validar credenciales: " . $e->getMessage());
            return false;
        }
    }

    // Método para obtener el tipo de usuario por correo
    public function obtenerTipoUsuarioPorCorreo($conexion) {
        try {
            $sql = "SELECT * FROM Usuarios WHERE correo = :correo";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':correo', $this->correo);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
            
        } catch (PDOException $e) {
            error_log("Error al obtener tipo de usuario: " . $e->getMessage());
            return null;
        }
    }

    // Método para actualizar la contraseña del usuario
    public function actualizarContrasena($conexion) {
        try {
            $sql = "UPDATE Usuarios SET contrasena = :contrasena WHERE correo = :correo";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':contrasena', $this->contrasena);
            $stmt->bindParam(':correo', $this->correo);
            
            $resultado = $stmt->execute();
            return $resultado;
            
        } catch (PDOException $e) {
            error_log("Error al actualizar contraseña: " . $e->getMessage());
            return false;
        }
    }

    public function consultarTodos($conexion) {
        
        $sql = "SELECT id_usuario, nombre, apellidos, correo, telefono, tipoUsuario, aceptado 
                FROM Usuarios";
        
        try {
            $stmt = $conexion->prepare($sql);
            $stmt->execute();

            $usuarios = [];
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                
                $row['aceptado'] = (bool) $row['aceptado'];
                $usuarios[] = $row;
            }
            
            return $usuarios;

        } catch (PDOException $e) {
        
            error_log("Error en consultarTodos: " . $e->getMessage());
            throw new Exception("Error al consultar la base de datos: " . $e->getMessage());
        }
    }

    public function eliminarPorId($conexion, $id_usuario) {
        
        $sql = "DELETE FROM Usuarios WHERE id_usuario = :id";

        try {
            $stmt = $conexion->prepare($sql);
            
            $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
            
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            error_log("Error en eliminarPorId: " . $e->getMessage());
            throw new Exception("Error al eliminar el usuario: " . $e->getMessage());
        }
    }

    public function consultarPorId($conexion, $id) {
        $sql = "SELECT nombre, apellidos, telefono, dato, correo, genero, fechaNacimiento, tipoUsuario 
                FROM Usuarios WHERE id_usuario = :id";
        try {
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$usuario) {
                throw new Exception('Usuario no encontrado');
            }
            return $usuario;
            
        } catch (PDOException $e) {
            throw new Exception("Error al consultar usuario: " . $e->getMessage());
        }
    }

    public function actualizarPerfilUsuario($conexion) {
        try {
      
            $sql = "UPDATE Usuarios SET 
                        nombre = :nombre,
                        apellidos = :apellidos,
                        telefono = :telefono,
                        correo = :correo";

            if ($this->getContrasena()) {
                $sql .= ", contrasena = :contrasena";
            }
            
            $sql .= " WHERE id_usuario = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellidos', $this->apellidos);
            $stmt->bindParam(':telefono', $this->telefono);
            $stmt->bindParam(':correo', $this->correo);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

            if ($this->getContrasena()) {
                $stmt->bindParam(':contrasena', $this->contrasena);
            }

            return $stmt->execute();

        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                throw new Exception("Error: El correo '{$this->correo}' ya está en uso.");
            }
            error_log("Error en actualizarPerfilUsuario: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarUsuario($conexion) {
        try {
            $sql = "UPDATE Usuarios SET 
                        nombre = :nombre,
                        apellidos = :apellidos,
                        tipoUsuario = :tipoUsuario,
                        telefono = :telefono,
                        dato = :dato,
                        correo = :correo,
                        genero = :genero,
                        fechaNacimiento = :fechaNacimiento";

            if ($this->getContrasena()) {
                $sql .= ", contrasena = :contrasena";
            }
            
            $sql .= " WHERE id_usuario = :id";

            $stmt = $conexion->prepare($sql);

            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellidos', $this->apellidos);
            $stmt->bindParam(':tipoUsuario', $this->tipoUsuario);
            $stmt->bindParam(':telefono', $this->telefono);
            $stmt->bindParam(':dato', $this->dato);
            $stmt->bindParam(':correo', $this->correo);
            $stmt->bindParam(':genero', $this->genero);
            $stmt->bindParam(':fechaNacimiento', $this->fechaNacimiento);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

            if ($this->getContrasena()) {
                $stmt->bindParam(':contrasena', $this->contrasena);
            }

            return $stmt->execute();

        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                throw new Exception("Error: El correo electrónico '{$this->correo}' ya está registrado por otro usuario.");
            }
            error_log("Error en actualizarUsuario: " . $e->getMessage());
            return false;
        }
    }

    public function consultarFavoritosPorUsuario($conexion, $id_usuario)
    {
        $sql = "SELECT 
                    r.id_recurso, 
                    r.titulo,
                    r.archivo_url
                FROM recurso r
                JOIN listasfavoritos f ON r.id_recurso = f.id_recurso
                WHERE f.id_usuario = :id_usuario
                ORDER BY r.id_recurso DESC";
        
        try {
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en consultarFavoritosPorUsuario: " . $e->getMessage());
            return []; 
        }
    }

    public function gestionarFavorito($conexion, $id_usuario, $id_recurso, $es_favorito)
    {
        try {
            if ($es_favorito) {
                $sql = "INSERT INTO listasfavoritos (id_usuario, id_recurso) 
                        VALUES (:id_usuario, :id_recurso)
                        ON DUPLICATE KEY UPDATE id_lista_favoritos = id_lista_favoritos";
                
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->bindParam(':id_recurso', $id_recurso, PDO::PARAM_INT);
                return $stmt->execute();

            } else {
                $sql = "DELETE FROM listasfavoritos 
                        WHERE id_usuario = :id_usuario AND id_recurso = :id_recurso";
                
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->bindParam(':id_recurso', $id_recurso, PDO::PARAM_INT);
                return $stmt->execute();
            }
        } catch (PDOException $e) {
            error_log("Error en gestionarFavorito: " . $e->getMessage());
            return false;
        }
    }

    public function consultarNoticiasDestacadas($conexion, $id_usuario){
        try{
            $sql = "SELECT 
                r.id_recurso, 
                r.titulo, 
                r.descripcion, 
                r.archivo_url,
                r.imagen_url,
                r.calificacion,
                (CASE WHEN f.id_usuario IS NOT NULL THEN 1 ELSE 0 END) as es_favorito 
            FROM Recurso r
            LEFT JOIN ListasFavoritos f 
                ON r.id_recurso = f.id_recurso AND f.id_usuario = :id_usuario
            INNER JOIN CategoriasUsuario cu 
                ON r.id_categoria = cu.id_categoria 
                AND cu.id_usuario = :id_usuario
            WHERE r.aprobado = 1
            ORDER BY r.calificacion DESC
            LIMIT 50";
            
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            
            // 1. Ejecutamos
            $stmt->execute();
            
            // 2. CORRECCIÓN: Devolvemos los datos, no el booleano
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        }catch(PDOException $e){
            error_log("Error en consultarNoticiasDestacadas: " . $e->getMessage());
            return []; // Mejor devolver array vacío que false para evitar errores en el foreach de la vista
        }
    }

} 

?>