<?php
/**
 * DataBaseModel
 * Encapsula la operación de generación de backup (dump) de la base de datos.
 * Nota: usa `mysqldump` del sistema. En entornos Windows con XAMPP asegúrate
 * de que `mysqldump.exe` esté en el PATH o ajusta la ruta en la configuración.
 */
class DataBaseModel {

    /**
     * Crea un volcado (dump) de la base de datos MySQL en la ruta indicada.
     * @param string $rutaCompleta Ruta absoluta del archivo destino (incluye nombre).
     * @return array ['exito' => bool, 'mensaje' => string]
     */
    public function crearBackup(string $rutaCompleta): array {
        // Cargar parámetros de conexión desde el archivo de configuración
        $configPath = __DIR__ . '/../../config/conexion.php';
        if (!file_exists($configPath)) {
            return ['exito' => false, 'mensaje' => 'No se encontró el archivo de configuración de la BD'];
        }

        // Incluir el archivo para obtener las variables de conexión
        // Este archivo define $servidor, $usuario, $contraseña, $base_de_datos
        require $configPath;

        if (!isset($servidor, $usuario, $contraseña, $base_de_datos)) {
            return ['exito' => false, 'mensaje' => 'Parámetros de conexión incompletos'];
        }

        // Verificar que la función exec esté disponible
        $disabled = array_map('trim', explode(',', ini_get('disable_functions')) ?: []);
        if (in_array('exec', $disabled)) {
            return ['exito' => false, 'mensaje' => 'La función exec() está deshabilitada en la configuración de PHP'];
        }

        // Construir el comando mysqldump de forma más robusta
        $hostEsc = escapeshellarg($servidor);
        $userEsc = escapeshellarg($usuario);
        $dbEsc = escapeshellarg($base_de_datos);

        // Password handling: usaremos --password=VALUE (más portable entre plataformas)
        $passPart = ' --password=';
        if ($contraseña !== null && $contraseña !== '') {
            $passPart .= escapeshellarg($contraseña);
        } else {
            $passPart .= escapeshellarg('');
        }

        // Asegurar que la carpeta destino exista
        $dir = dirname($rutaCompleta);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return ['exito' => false, 'mensaje' => 'No se pudo crear la carpeta de backups'];
            }
        }

        $rutaEsc = escapeshellarg($rutaCompleta);

        // Intentar localizar mysqldump en rutas comunes si no está en PATH (Windows XAMPP)
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

        // Construir el comando final (usando --password= para evitar prompts)
        $command = "$mysqldumpEsc -h $hostEsc -u $userEsc$passPart $dbEsc > $rutaEsc";

        // Ejecutar el comando y capturar salida y código de retorno
        exec($command . ' 2>&1', $output, $returnVar);

        if ($returnVar !== 0) {
            // Devolver salida de error para diagnóstico
            $msg = implode("\n", $output);
            return ['exito' => false, 'mensaje' => 'Error al ejecutar mysqldump: ' . $msg];
        }

        // Verificar que el archivo se creó y tiene contenido
        if (!file_exists($rutaCompleta) || filesize($rutaCompleta) === 0) {
            return ['exito' => false, 'mensaje' => 'El backup no se creó o está vacío'];
        }

        return ['exito' => true, 'mensaje' => 'Backup generado correctamente'];
    }
}

?>
