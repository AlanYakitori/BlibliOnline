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
}
?>