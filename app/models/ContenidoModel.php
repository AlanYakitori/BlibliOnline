<?php
require_once __DIR__ . '/../../config/conexion.php';

class ContenidoModel {
    private $id_recurso;
    private $titulo;
    private $descripcion;
    private $archivo_url;
    private $calificacion;
    private $aprobado;
    private $id_categoria;
    private $id_usuario;

    public function __construct() {
        $this->id_recurso = 0;
        $this->titulo = '';
        $this->descripcion = '';
        $this->archivo_url = '';
        $this->calificacion = 0;
        $this->aprobado = 0;
        $this->id_categoria = 0;
        $this->id_usuario = 0;
    }

    // Getters
    public function getIdRecurso() { return $this->id_recurso; }
    public function getTitulo() { return $this->titulo; }
    public function getDescripcion() { return $this->descripcion; }
    public function getArchivoUrl() { return $this->archivo_url; }
    public function getCalificacion() { return $this->calificacion; }
    public function getAprobado() { return $this->aprobado; }
    public function getIdCategoria() { return $this->id_categoria; }
    public function getIdUsuario() { return $this->id_usuario; }

    // Setters
    public function setIdRecurso($id_recurso) { $this->id_recurso = $id_recurso; }
    public function setTitulo($titulo) { $this->titulo = $titulo; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }
    public function setArchivoUrl($archivo_url) { $this->archivo_url = $archivo_url; }
    public function setCalificacion($calificacion) { $this->calificacion = $calificacion; }
    public function setAprobado($aprobado) { $this->aprobado = $aprobado; }
    public function setIdCategoria($id_categoria) { $this->id_categoria = $id_categoria; }
    public function setIdUsuario($id_usuario) { $this->id_usuario = $id_usuario; }
}
?>