<?php
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../public/libraries/PlantillaReporte.php'; 

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

    // =========================================================
    // 1. REPORTE RECURSOS (Estadísticas Generales)
    // =========================================================
    public function reporteRecursos() {
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(0, 10, mb_convert_encoding('Estadísticas de Recursos', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->pdf->Ln(5);

        // --- A. PASTEL: Recursos por Categoría ---
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetTextColor(0, 102, 204);
        $this->pdf->Cell(0, 10, mb_convert_encoding('1. Distribución por Categoría', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $this->pdf->SetTextColor(0);

        $sql = "SELECT c.nombre as label, COUNT(r.id_recurso) as valor 
                FROM Recurso r 
                INNER JOIN Categoria c ON r.id_categoria = c.id_categoria 
                GROUP BY c.nombre";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if(!empty($datos)) {
            // Nota: Asegúrate que tu PlantillaReporte tenga el método Sector, 
            // si no, usa crearGraficaBarras en su lugar.
            $this->crearGraficaPastel($datos); 
        } else {
            $this->pdf->Cell(0,10,'No hay datos para graficar.',0,1);
        }
        $this->pdf->Ln(10);

        // --- B. TABLA: Top 5 Favoritos ---
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetTextColor(0, 102, 204);
        $this->pdf->Cell(0, 10, mb_convert_encoding('2. Top 5 Recursos Más Populares', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $this->pdf->SetTextColor(0);

        $this->pdf->SetFillColor(0, 102, 204);
        $this->pdf->SetTextColor(255, 255, 255);
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(100, 10, mb_convert_encoding('Título', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $this->pdf->Cell(50, 10, mb_convert_encoding('Categoría', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $this->pdf->Cell(40, 10, 'Total Favoritos', 1, 1, 'C', true);
        
        $this->pdf->SetTextColor(0);
        $this->pdf->SetFont('Arial', '', 10);

        $sqlTop = "SELECT r.titulo, c.nombre as nombre_cat, COUNT(f.id_lista_favoritos) as total 
                   FROM Recurso r 
                   LEFT JOIN ListasFavoritos f ON r.id_recurso = f.id_recurso 
                   LEFT JOIN Categoria c ON r.id_categoria = c.id_categoria
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
            $this->pdf->Ln();
            $fill = !$fill;
        }
        $this->pdf->Output('I', 'Reporte_Recursos.pdf');
    }

    // =========================================================
    // 2. REPORTE GRUPOS (Vista Admin - General)
    // =========================================================
    public function reporteGrupos() {
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(0, 10, mb_convert_encoding('Estado General de Grupos', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->pdf->Ln(5);

        // SQL corregido a tu estructura: Grupos -> Miembros -> Usuarios
        $sql = "SELECT g.nombre AS nombre_grupo, g.clave AS codigo_grupo,
                COUNT(mg.id_usuario) as total_alumnos
                FROM Grupos g
                LEFT JOIN MiembrosGrupo mg ON g.id_grupo = mg.id_grupo
                GROUP BY g.id_grupo";
        
        $this->pdf->SetFillColor(0, 102, 204);
        $this->pdf->SetTextColor(255, 255, 255);
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(100, 10, 'Nombre del Grupo', 1, 0, 'C', true);
        $this->pdf->Cell(50, 10, 'Clave', 1, 0, 'C', true);
        $this->pdf->Cell(40, 10, 'Inscritos', 1, 1, 'C', true);
        
        $this->pdf->SetTextColor(0);
        $this->pdf->SetFont('Arial', '', 10);

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
             $this->pdf->Cell(100, 10, mb_convert_encoding($row['nombre_grupo'], 'ISO-8859-1', 'UTF-8'), 1);
             $this->pdf->Cell(50, 10, $row['codigo_grupo'], 1, 0, 'C');
             $this->pdf->Cell(40, 10, $row['total_alumnos'], 1, 1, 'C');
        }
        $this->pdf->Output('I', 'Reporte_Grupos_Admin.pdf');
    }

    // =========================================================
    // 3. REPORTE USUARIOS (Demografía General)
    // =========================================================
    public function reporteUsuarios() {
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(0, 10, mb_convert_encoding('Demografía de Usuarios', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->pdf->Ln(5);

        // --- Roles ---
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetTextColor(0, 102, 204);
        $this->pdf->Cell(0, 10, '1. Distribucion por Rol', 0, 1, 'L');
        $this->pdf->SetTextColor(0);
        
        $sql = "SELECT tipoUsuario as label, COUNT(*) as valor FROM Usuarios GROUP BY tipoUsuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        // Usamos barras aquí para evitar errores si falla Sector
        $this->crearGraficaBarras($stmt->fetchAll(PDO::FETCH_ASSOC)); 
        $this->pdf->Ln(10);

        // --- Género ---
        if($this->pdf->GetY() > 180) $this->pdf->AddPage();
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetTextColor(0, 102, 204);
        $this->pdf->Cell(0, 10, mb_convert_encoding('2. Distribución por Género', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $this->pdf->SetTextColor(0);

        $sql2 = "SELECT genero as label, COUNT(*) as valor FROM Usuarios GROUP BY genero";
        $stmt2 = $this->conexion->prepare($sql2);
        $stmt2->execute();
        $datosGenero = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        foreach($datosGenero as &$d) { if(empty($d['label'])) $d['label']='Sin Especificar'; }

        $this->crearGraficaBarras($datosGenero);

        $this->pdf->Output('I', 'Reporte_Usuarios.pdf');
    }

    public function reporteActividadDocente($id_docente) {
        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(0, 10, mb_convert_encoding('Reporte de Actividad Docente', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->pdf->Ln(5);

        // --- A. MIS GRUPOS (Resumen) ---
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetTextColor(0, 102, 204);
        $this->pdf->Cell(0, 10, mb_convert_encoding('1. Resumen de Mis Grupos', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $this->pdf->SetTextColor(0);

        $sqlGrupos = "SELECT g.id_grupo, g.nombre, g.clave, COUNT(mg.id_usuario) as total
                      FROM Grupos g
                      LEFT JOIN MiembrosGrupo mg ON g.id_grupo = mg.id_grupo
                      WHERE g.docente = :docente
                      GROUP BY g.id_grupo";
        $stmt = $this->conexion->prepare($sqlGrupos);
        $stmt->bindParam(':docente', $id_docente);
        $stmt->execute();
        $misGrupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->pdf->SetFillColor(230, 230, 230);
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(80, 10, 'Grupo', 1, 0, 'C', true);
        $this->pdf->Cell(50, 10, 'Clave', 1, 0, 'C', true);
        $this->pdf->Cell(60, 10, 'Total Alumnos', 1, 1, 'C', true);
        $this->pdf->SetFont('Arial', '', 10);

        foreach($misGrupos as $g) {
            $this->pdf->Cell(80, 10, mb_convert_encoding($g['nombre'], 'ISO-8859-1', 'UTF-8'), 1);
            $this->pdf->Cell(50, 10, $g['clave'], 1, 0, 'C');
            $this->pdf->Cell(60, 10, $g['total'], 1, 1, 'C');
        }
        $this->pdf->Ln(15);

        // --- B. GRÁFICA: Hombres vs Mujeres (En mis clases) ---
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetTextColor(0, 102, 204);
        $this->pdf->Cell(0, 10, mb_convert_encoding('2. Género de mis Alumnos', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $this->pdf->SetTextColor(0);

        $sqlGen = "SELECT u.genero as label, COUNT(u.id_usuario) as valor
                   FROM Usuarios u
                   INNER JOIN MiembrosGrupo mg ON u.id_usuario = mg.id_usuario
                   INNER JOIN Grupos g ON mg.id_grupo = g.id_grupo
                   WHERE g.docente = :docente
                   GROUP BY u.genero";
        $stmt = $this->conexion->prepare($sqlGen);
        $stmt->bindParam(':docente', $id_docente);
        $stmt->execute();
        $dataGen = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if(!empty($dataGen)) {
            foreach($dataGen as &$d) { if(empty($d['label'])) $d['label']='Sin Especificar'; }
            $this->crearGraficaBarras($dataGen);
        } else {
            $this->pdf->Cell(0,10,'No tienes alumnos registrados aun.',0,1);
        }

        // --- C. LISTAS DE ASISTENCIA (Detalle) ---
        if($this->pdf->GetY() > 200) $this->pdf->AddPage();
        $this->pdf->Ln(10);
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->SetTextColor(0, 102, 204);
        $this->pdf->Cell(0, 10, mb_convert_encoding('3. Listas de Alumnos por Grupo', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
        $this->pdf->SetTextColor(0);

        foreach($misGrupos as $grupo) {
            $this->pdf->SetFont('Arial', 'B', 11);
            $this->pdf->SetFillColor(200, 220, 255);
            $this->pdf->Cell(0, 8, mb_convert_encoding('Grupo: ' . $grupo['nombre'], 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);

            $sqlList = "SELECT u.nombre, u.apellidos, u.dato as matricula, u.genero 
                        FROM Usuarios u
                        INNER JOIN MiembrosGrupo mg ON u.id_usuario = mg.id_usuario
                        WHERE mg.id_grupo = :idg
                        ORDER BY u.apellidos ASC";
            $stmt = $this->conexion->prepare($sqlList);
            $stmt->bindParam(':idg', $grupo['id_grupo']);
            $stmt->execute();

            $this->pdf->SetFont('Arial', 'B', 9);
            $this->pdf->Cell(40, 6, mb_convert_encoding('Dato/Matrícula', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $this->pdf->Cell(110, 6, 'Nombre Completo', 1, 0, 'C');
            $this->pdf->Cell(40, 6, mb_convert_encoding('Género', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C');
            
            $this->pdf->SetFont('Arial', '', 9);
            while($alum = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $nom = mb_convert_encoding($alum['apellidos'] . ' ' . $alum['nombre'], 'ISO-8859-1', 'UTF-8');
                $this->pdf->Cell(40, 6, $alum['matricula'], 1, 0, 'C');
                $this->pdf->Cell(110, 6, $nom, 1, 0, 'L');
                $this->pdf->Cell(40, 6, $alum['genero'], 1, 1, 'C');
            }
            $this->pdf->Ln(8);
        }

        $this->pdf->Output('I', 'Reporte_Docente.pdf');
    }


    // =========================================================
    // HELPERS (Gráficas)
    // =========================================================

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
            
            // Intento de llamada segura
            if(method_exists($this->pdf, 'Sector')) {
                $this->pdf->Sector($centroX, $centroY, $radio, $anguloInicio, $anguloInicio + $angulo, 'F');
            } else {
                // Fallback simple si no existe Sector: un rectángulo del color
                 $this->pdf->Rect($centroX, $centroY + ($i*5), 10, 10, 'F'); 
            }

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

    private function crearGraficaBarras($datos) {
        if(empty($datos)) return;
        $anchoBarra = 30; 
        $espacioEntreBarras = 15; 
        $altoMaximo = 50;
        $inicioX = 25; 
        $inicioY = $this->pdf->GetY() + 10;
        
        $maxValor = 0;
        foreach ($datos as $d) { if ($d['valor'] > $maxValor) $maxValor = $d['valor']; }
        if ($maxValor == 0) $maxValor = 1;
        
        $this->pdf->SetDrawColor(50, 50, 50); 
        $this->pdf->SetLineWidth(0.3);
        $this->pdf->Line($inicioX, $inicioY, $inicioX, $inicioY + $altoMaximo);
        $this->pdf->Line($inicioX, $inicioY + $altoMaximo, $inicioX + (count($datos) * ($anchoBarra + $espacioEntreBarras)) + 10, $inicioY + $altoMaximo);
        
        $actualX = $inicioX + 10; 
        $this->pdf->SetFont('Arial', '', 9);
        
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