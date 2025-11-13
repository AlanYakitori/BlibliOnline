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

    public function restaurarBackup(string $rutaSqlCompleta): array {
        // Verificar que el archivo existe
        if (!file_exists($rutaSqlCompleta)) {
            return ['exito' => false, 'mensaje' => 'El archivo de restauración no existe'];
        }

        // Verificar que el archivo no está vacío
        if (filesize($rutaSqlCompleta) === 0) {
            return ['exito' => false, 'mensaje' => 'El archivo de restauración está vacío'];
        }

        // Cargar parámetros de conexión desde el archivo de configuración
        $configPath = __DIR__ . '/../../config/conexion.php';
        if (!file_exists($configPath)) {
            return ['exito' => false, 'mensaje' => 'No se encontró el archivo de configuración de la BD'];
        }

        require $configPath;

        if (!isset($servidor, $usuario, $contraseña, $base_de_datos)) {
            return ['exito' => false, 'mensaje' => 'Parámetros de conexión incompletos'];
        }

        // Verificar que la función exec esté disponible
        $disabled = array_map('trim', explode(',', ini_get('disable_functions')) ?: []);
        if (in_array('exec', $disabled)) {
            return ['exito' => false, 'mensaje' => 'La función exec() está deshabilitada en la configuración de PHP'];
        }

        // Construir el comando mysql para restauración
        $hostEsc = escapeshellarg($servidor);
        $userEsc = escapeshellarg($usuario);
        $dbEsc = escapeshellarg($base_de_datos);
        $sqlFileEsc = escapeshellarg($rutaSqlCompleta);

        // Password handling para mysql
        $passPart = '';
        if ($contraseña !== null && $contraseña !== '') {
            $passPart = ' --password=' . escapeshellarg($contraseña);
        } else {
            $passPart = ' --password=' . escapeshellarg('');
        }

        // Localizar mysql en rutas comunes (Windows XAMPP)
        $mysqlCmd = 'mysql';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $candidates = [
                getenv('MYSQL_PATH'),
                'C:\\xampp\\mysql\\bin\\mysql.exe',
                'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysql.exe',
                'C:\\Program Files (x86)\\MySQL\\MySQL Server 5.7\\bin\\mysql.exe'
            ];
            foreach ($candidates as $c) {
                if (!$c) continue;
                if (file_exists($c)) {
                    $mysqlCmd = $c;
                    break;
                }
            }
        }

        $mysqlEsc = escapeshellarg($mysqlCmd);

        // Comando final: mysql -h host -u user --password=pass database < archivo.sql
        $command = "$mysqlEsc -h $hostEsc -u $userEsc$passPart $dbEsc < $sqlFileEsc";

        // Ejecutar el comando y capturar salida y código de retorno
        exec($command . ' 2>&1', $output, $returnVar);

        if ($returnVar !== 0) {
            // Error en la restauración
            $msg = implode("\n", $output);
            return ['exito' => false, 'mensaje' => 'Error al ejecutar la restauración: ' . $msg];
        }

        return ['exito' => true, 'mensaje' => 'Base de datos restaurada correctamente'];
    }
}

?>
