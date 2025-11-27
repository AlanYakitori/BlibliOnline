<?php
require_once __DIR__ . '/../../public/libraries/fpdf/fpdf.php';

class PlantillaReporte extends FPDF {
    
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'BIBLIONLINE - SISTEMA DE GESTION', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'Reporte Generado el: ' . date('d/m/Y H:i'), 0, 1, 'C');
        $this->Ln(5);
        $this->SetDrawColor(0, 123, 255);
        $this->SetLineWidth(1);
        $this->Line(10, 30, 200, 30); 
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, mb_convert_encoding('Página ', 'ISO-8859-1', 'UTF-8') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function Sector($xc, $yc, $r, $a, $b, $style='FD', $cw=true, $o=90) {
        $d0 = $a - $b;
        if($cw){
            $d = $b; $b = $o - $a; $a = $o - $d;
        } else {
            $b += $o; $a += $o;
        }
        while($a<0) $a += 360;
        while($a>360) $a -= 360;
        while($b<0) $b += 360;
        while($b>360) $b -= 360;
        if ($a > $b) $b += 360;
        $b = $b/360*2*M_PI;
        $a = $a/360*2*M_PI;
        $d = $b - $a;
        if ($d == 0 && $d0 != 0) $d = 2*M_PI;
        $k = $this->k; // AHORA SÍ FUNCIONA
        $hp = $this->h; // AHORA SÍ FUNCIONA
        if (sin($d/2)) {
            $MyArc = 4/3 * (1-cos($d/2)) / sin($d/2) * $r;
        } else {
            $MyArc = 0;
        }
        $this->_out(sprintf('%.2F %.2F m',($xc)*$k,($hp-$yc)*$k)); // AHORA SÍ FUNCIONA
        $this->_out(sprintf('%.2F %.2F l',($xc+$r*cos($a))*$k,($hp-($yc-$r*sin($a)))*$k));
        if ($d < M_PI/2) {
            $this->Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                        $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                        $xc+$r*cos($b)+$MyArc*cos(M_PI/2+$b),
                        $yc-$r*sin($b)-$MyArc*sin(M_PI/2+$b),
                        $xc+$r*cos($b),
                        $yc-$r*sin($b)
                        );
        } else {
            $b = $a + $d/4;
            $MyArc = 4/3 * (1-cos($d/8)) / sin($d/8) * $r;
            $this->Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                        $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                        $xc+$r*cos($b)+$MyArc*cos(M_PI/2+$b),
                        $yc-$r*sin($b)-$MyArc*sin(M_PI/2+$b),
                        $xc+$r*cos($b),
                        $yc-$r*sin($b)
                        );
            $a = $b; $b = $a + $d/4;
            $this->Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                        $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                        $xc+$r*cos($b)+$MyArc*cos(M_PI/2+$b),
                        $yc-$r*sin($b)-$MyArc*sin(M_PI/2+$b),
                        $xc+$r*cos($b),
                        $yc-$r*sin($b)
                        );
            $a = $b; $b = $a + $d/4;
            $this->Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                        $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                        $xc+$r*cos($b)+$MyArc*cos(M_PI/2+$b),
                        $yc-$r*sin($b)-$MyArc*sin(M_PI/2+$b),
                        $xc+$r*cos($b),
                        $yc-$r*sin($b)
                        );
            $a = $b; $b = $a + $d/4;
            $this->Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                        $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                        $xc+$r*cos($b)+$MyArc*cos(M_PI/2+$b),
                        $yc-$r*sin($b)-$MyArc*sin(M_PI/2+$b),
                        $xc+$r*cos($b),
                        $yc-$r*sin($b)
                        );
        }
        // terminate
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='b';
        else
            $op='s';
        $this->_out($op);
    }

    function Arc($x1, $y1, $x2, $y2, $x3, $y3) {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', 
            $x1*$this->k, 
            ($h-$y1)*$this->k,
            $x2*$this->k, 
            ($h-$y2)*$this->k,
            $x3*$this->k, 
            ($h-$y3)*$this->k));
    }
}
?>