<?php
require_once __DIR__ . '/../../controllers/ReportController.php';

if (isset($_GET['tipo'])) {
    $reporte = new ReportController();
    
    switch ($_GET['tipo']) {
        case 'recursos':
            $reporte->reporteRecursos();
            break;
        case 'grupos':
            $reporte->reporteGrupos();
            break;
        case 'usuarios':
            $reporte->reporteUsuarios();
            break;
        default:
            echo "Tipo de reporte no válido.";
    }
} else {
    echo "Error: No especificaste el reporte.";
}
?>