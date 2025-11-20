<?php
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../public/libraries/PlantillaReporte.php'; 
// OJO: Ajusta la ruta de arriba según donde hayas guardado PlantillaReporte.php (Opción A o B del paso anterior)

class ReportController {
    private $pdf;
    private $conexion;

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
        $this->pdf = new PlantillaReporte();
        $this->pdf->AliasNbPages();
        $this->pdf->AddPage();
    }

    // 1. REPORTE RECURSOS
    public function reporteRecursos() {
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(0, 10, mb_convert_encoding('Estadísticas de Recursos', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->pdf->Ln(5);

        // --- PASTEL: Recursos por Categoría ---
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetTextColor(0, 123, 255);
        $this->pdf->Cell(0, 10, mb_convert_encoding('1. Distribución por Categoría', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $this->pdf->SetTextColor(0);

        $sql = "SELECT c.nombre as label, COUNT(r.id_recurso) as valor 
                FROM recurso r 
                INNER JOIN categoria c ON r.id_categoria = c.id_categoria 
                GROUP BY c.nombre";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if(empty($datos)) {
            $this->pdf->Cell(0,10,'No hay datos.',0,1);
        } else {
            $this->crearGraficaPastel($datos); // Llamamos al helper local
        }
        $this->pdf->Ln(10);

        // --- TABLA: Top 5 Favoritos ---
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetTextColor(0, 123, 255);
        $this->pdf->Cell(0, 10, mb_convert_encoding('2. Top 5 Recursos Más Populares', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $this->pdf->SetTextColor(0);

        $this->pdf->SetFillColor(0, 123, 255);
        $this->pdf->SetTextColor(255, 255, 255);
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(100, 10, mb_convert_encoding('Título', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $this->pdf->Cell(50, 10, mb_convert_encoding('Categoría', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $this->pdf->Cell(40, 10, 'Total Favoritos', 1, 1, 'C', true);
        
        $this->pdf->SetTextColor(0);
        $this->pdf->SetFont('Arial', '', 10);

        $sqlTop = "SELECT r.titulo, c.nombre as nombre_cat, COUNT(f.id_lista_favoritos) as total 
                   FROM recurso r 
                   LEFT JOIN listasfavoritos f ON r.id_recurso = f.id_recurso 
                   LEFT JOIN categoria c ON r.id_categoria = c.id_categoria
                   GROUP BY r.id_recurso 
                   ORDER BY total DESC LIMIT 5";
        $stmt = $this->conexion->prepare($sqlTop);
        $stmt->execute();
        $fill = false;
        $this->pdf->SetFillColor(240, 240, 240);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $titulo = mb_convert_encoding(substr($row['titulo'], 0, 50), 'ISO-8859-1', 'UTF-8');
            $cat = mb_convert_encoding($row['nombre_cat'] ?? 'General', 'ISO-8859-1', 'UTF-8');
            $this->pdf->Cell(100, 10, $titulo, 1, 0, 'L', $fill);
            $this->pdf->Cell(50, 10, $cat, 1, 0, 'C', $fill);
            $this->pdf->Cell(40, 10, $row['total'], 1, 1, 'C', $fill);
            $fill = !$fill;
        }
        $this->pdf->Output('I', 'Reporte_Recursos.pdf');
    }

    // 2. REPORTE GRUPOS (Igual que antes, sin cambios)
    public function reporteGrupos() {
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(0, 10, mb_convert_encoding('Estado de Grupos y Alumnos', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->pdf->Ln(5);
        // ... (Resto del código de grupos que ya funcionaba bien)
        // Copia aquí la lógica de la tabla de grupos del archivo anterior
        // ...
        
        // SQL DE GRUPOS QUE CORREGIMOS
        $sql = "SELECT g.nombre AS nombre_grupo, g.clave AS codigo_grupo,
                SUM(CASE WHEN u.aceptado = 1 THEN 1 ELSE 0 END) as activos,
                SUM(CASE WHEN u.aceptado = 0 THEN 1 ELSE 0 END) as pendientes
                FROM Grupos g
                LEFT JOIN MiembrosGrupo mg ON g.id_grupo = mg.id_grupo
                LEFT JOIN Usuarios u ON mg.id_usuario = u.id_usuario
                GROUP BY g.id_grupo, g.nombre, g.clave";
        
        $this->pdf->SetFillColor(40, 167, 69);
        $this->pdf->SetTextColor(255, 255, 255);
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(70, 10, 'Nombre del Grupo', 1, 0, 'C', true);
        $this->pdf->Cell(40, 10, 'Codigo', 1, 0, 'C', true);
        $this->pdf->Cell(40, 10, 'Alumnos', 1, 0, 'C', true);
        $this->pdf->Cell(40, 10, 'Pendientes', 1, 1, 'C', true);
        $this->pdf->SetTextColor(0);
        $this->pdf->SetFont('Arial', '', 10);

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
             $this->pdf->Cell(70, 10, mb_convert_encoding($row['nombre_grupo'], 'ISO-8859-1', 'UTF-8'), 1);
             $this->pdf->Cell(40, 10, $row['codigo_grupo'], 1, 0, 'C');
             $this->pdf->Cell(40, 10, $row['activos'], 1, 0, 'C');
             if($row['pendientes']>0) $this->pdf->SetTextColor(220,0,0);
             $this->pdf->Cell(40, 10, $row['pendientes'], 1, 1, 'C');
             $this->pdf->SetTextColor(0);
        }
        $this->pdf->Output('I', 'Reporte_Grupos.pdf');
    }

    // 3. REPORTE USUARIOS
    public function reporteUsuarios() {
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(0, 10, mb_convert_encoding('Demografía de Usuarios', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->pdf->Ln(5);

        // --- PASTEL: Roles ---
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetTextColor(0, 123, 255);
        $this->pdf->Cell(0, 10, '1. Distribucion por Rol', 0, 1, 'L');
        $this->pdf->SetTextColor(0);
        
        $sql = "SELECT tipoUsuario as label, COUNT(*) as valor FROM usuarios GROUP BY tipoUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $this->crearGraficaPastel($stmt->fetchAll(PDO::FETCH_ASSOC));
        $this->pdf->Ln(10);

        // --- BARRAS: Género ---
        if($this->pdf->GetY() > 180) $this->pdf->AddPage();
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetTextColor(0, 123, 255);
        $this->pdf->Cell(0, 10, mb_convert_encoding('2. Distribución por Género', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $this->pdf->SetTextColor(0);

        $sql2 = "SELECT genero as label, COUNT(*) as valor FROM usuarios GROUP BY genero";
        $stmt2 = $this->conexion->prepare($sql2);
        $stmt2->execute();
        $datosGenero = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        foreach($datosGenero as &$d) { if(empty($d['label'])) $d['label']='Sin Especificar'; }

        $this->crearGraficaBarras($datosGenero); // Barras normales

        $this->pdf->Output('I', 'Reporte_Usuarios.pdf');
    }

    // ==========================================
    // HELPER PARA PASTEL (Llama a la librería)
    // ==========================================
    private function crearGraficaPastel($datos) {
        if(empty($datos)) return;
        $radio = 30; 
        $centroX = 60;
        $centroY = $this->pdf->GetY() + 35;
        $leyendaX = 110;
        $leyendaY = $this->pdf->GetY() + 10;

        $total = 0;
        foreach ($datos as $d) { $total += $d['valor']; }

        $colores = [
            [54, 162, 235], [255, 99, 132], [255, 205, 86],
            [75, 192, 192], [153, 102, 255], [255, 159, 64]
        ];

        $anguloInicio = 0;
        $i = 0;

        foreach ($datos as $d) {
            $angulo = ($d['valor'] * 360) / $total;
            $color = $colores[$i % count($colores)];
            
            $this->pdf->SetFillColor($color[0], $color[1], $color[2]);
            $this->pdf->SetDrawColor(255, 255, 255);

            // AQUÍ ESTÁ EL CAMBIO CLAVE: LLAMAR A $this->pdf->Sector
            $this->pdf->Sector($centroX, $centroY, $radio, $anguloInicio, $anguloInicio + $angulo, 'F');

            // Leyenda
            $this->pdf->SetXY($leyendaX, $leyendaY + ($i * 8));
            $this->pdf->Rect($leyendaX, $leyendaY + ($i * 8), 5, 5, 'F');
            $this->pdf->SetXY($leyendaX + 8, $leyendaY + ($i * 8));
            $porcentaje = number_format(($d['valor'] / $total) * 100, 1);
            $label = mb_convert_encoding(ucfirst($d['label']), 'ISO-8859-1', 'UTF-8');
            $this->pdf->Cell(0, 5, "$label - $porcentaje%", 0, 1);

            $anguloInicio += $angulo;
            $i++;
        }
        $this->pdf->SetY($centroY + $radio + 10);
    }

    // Helper Barras (se queda igual)
    private function crearGraficaBarras($datos) {
        // ... (Tu código de barras que ya funcionaba, cópialo del mensaje anterior) ...
        // Resumen rápido para que no falle si copias todo:
        if(empty($datos)) return;
        $anchoBarra = 30; $espacioEntreBarras = 15; $altoMaximo = 60;
        $inicioX = 25; $inicioY = $this->pdf->GetY() + 10;
        $maxValor = 0;
        foreach ($datos as $d) { if ($d['valor'] > $maxValor) $maxValor = $d['valor']; }
        if ($maxValor == 0) $maxValor = 1;
        $this->pdf->SetDrawColor(50, 50, 50); $this->pdf->SetLineWidth(0.3);
        $this->pdf->Line($inicioX, $inicioY, $inicioX, $inicioY + $altoMaximo);
        $this->pdf->Line($inicioX, $inicioY + $altoMaximo, $inicioX + (count($datos) * ($anchoBarra + $espacioEntreBarras)) + 10, $inicioY + $altoMaximo);
        $actualX = $inicioX + 10; $this->pdf->SetFont('Arial', '', 9);
        foreach ($datos as $d) {
            $altura = ($d['valor'] * $altoMaximo) / $maxValor;
            $posY = ($inicioY + $altoMaximo) - $altura;
            $this->pdf->SetFillColor(54, 162, 235);
            $this->pdf->Rect($actualX, $posY, $anchoBarra, $altura, 'F');
            $this->pdf->SetXY($actualX, $posY - 5);
            $this->pdf->Cell($anchoBarra, 5, $d['valor'], 0, 0, 'C');
            $this->pdf->SetXY($actualX, $inicioY + $altoMaximo + 2);
            $label = mb_convert_encoding(ucfirst($d['label']), 'ISO-8859-1', 'UTF-8');
            if(strlen($label) > 12) $label = substr($label, 0, 10) . '..';
            $this->pdf->Cell($anchoBarra, 5, $label, 0, 0, 'C');
            $actualX += ($anchoBarra + $espacioEntreBarras);
        }
        $this->pdf->SetY($inicioY + $altoMaximo + 20);
    }
}
?>