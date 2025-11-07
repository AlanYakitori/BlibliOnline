<?php
class DataBaseModel {
    public function crearBackup(string $rutaCompleta): array {

        $configPath = __DIR__ . '/../../config/conexion.php';
        if (!file_exists($configPath)) {
            return ['exito' => false, 'mensaje' => 'No se encontró el archivo de configuración de la BD'];
        }

        require $configPath;

        if (!isset($servidor, $usuario, $contraseña, $base_de_datos)) {
            return ['exito' => false, 'mensaje' => 'Parámetros de conexión incompletos'];
        }

        $disabled = array_map('trim', explode(',', ini_get('disable_functions')) ?: []);
        if (in_array('exec', $disabled)) {
            return ['exito' => false, 'mensaje' => 'La función exec() está deshabilitada en la configuración de PHP'];
        }

        $hostEsc = escapeshellarg($servidor);
        $userEsc = escapeshellarg($usuario);
        $dbEsc = escapeshellarg($base_de_datos);

        $passPart = ' --password=';
        if ($contraseña !== null && $contraseña !== '') {
            $passPart .= escapeshellarg($contraseña);
        } else {
            $passPart .= escapeshellarg('');
        }

        $dir = dirname($rutaCompleta);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return ['exito' => false, 'mensaje' => 'No se pudo crear la carpeta de backups'];
            }
        }

        $rutaEsc = escapeshellarg($rutaCompleta);

        $mysqldumpCmd = 'mysqldump';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $candidates = [
                getenv('MYSQLDUMP_PATH'),
                'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
                'C:\\Program Files (x86)\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe'
            ];
            foreach ($candidates as $c) {
                if (!$c) continue;
                if (file_exists($c)) {
                    $mysqldumpCmd = $c;
                    break;
                }
            }
        }

        $mysqldumpEsc = escapeshellarg($mysqldumpCmd);

        $command = "$mysqldumpEsc -h $hostEsc -u $userEsc$passPart $dbEsc > $rutaEsc";

        exec($command . ' 2>&1', $output, $returnVar);

        if ($returnVar !== 0) {
            $msg = implode("\n", $output);
            return ['exito' => false, 'mensaje' => 'Error al ejecutar mysqldump: ' . $msg];
        }

        if (!file_exists($rutaCompleta) || filesize($rutaCompleta) === 0) {
            return ['exito' => false, 'mensaje' => 'El backup no se creó o está vacío'];
        }

        return ['exito' => true, 'mensaje' => 'Backup generado correctamente'];
    }
}

?>
