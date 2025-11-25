<?php
// 1. INICIAR SESIÓN (Obligatorio para obtener el ID del docente)
session_start();

require_once __DIR__ . '/../../controllers/ReportController.php';

if (isset($_GET['tipo'])) {
    $reporte = new ReportController();
    
    switch ($_GET['tipo']) {
        // Reportes Generales (Admin)
        case 'recursos':
            $reporte->reporteRecursos();
            break;

        case 'grupos':
            $reporte->reporteGrupos();
            break;

        case 'usuarios':
            $reporte->reporteUsuarios();
            break;

        case 'docente':
            // TRUCO: Si $_SESSION['id_usuario'] existe, lo usa.
            // Si no (porque no te logueaste), usa el 4 (Arantxa) automáticamente.
            $id_docente = $_SESSION['id_usuario'] ?? 4; 
            
            $reporte->reporteActividadDocente($id_docente);
            break;

        default:
            echo "Tipo de reporte no válido.";
            break;
    }
} else {
    echo "Error: No especificaste el reporte.";
}
?>