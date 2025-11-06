<?php
// Gestión centralizada de sesiones y funciones de seguridad
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', 1);
    // Configurar cookies de sesión con seguridad básica
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

function iniciarSesionUsuario(array $usuario){
    if (session_status() == PHP_SESSION_NONE) session_start();
    session_regenerate_id(true);
    $_SESSION['usuario'] = $usuario;
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
        }
    }
}

function protegerPagina(array $rolesPermitidos = []){
    // Evitar cache del lado del navegador para que el back no muestre contenido válido
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');

    if (session_status() == PHP_SESSION_NONE) session_start();

    if (empty($_SESSION['usuario'])) {
        header('Location: ../../../index.php');
        exit;
    }

    if (!empty($rolesPermitidos) && !in_array($_SESSION['usuario']['tipoUsuario'], $rolesPermitidos)) {
        // Si el rol no está permitido, cerrar sesión por seguridad y redirigir
        cerrarSesion();
        header('Location: ../../../index.php');
        exit;
    }
}

/**
 * Cierra la sesión de forma segura.
 */
function cerrarSesion(){
    if (session_status() == PHP_SESSION_NONE) session_start();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'], $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

function obtenerCSRFToken(){
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
        }
    }
    return $_SESSION['csrf_token'];
}

function verificarCSRFToken($token){
    if (session_status() == PHP_SESSION_NONE) session_start();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

?>
