document.addEventListener('DOMContentLoaded', function() {
    // Verificar que el usuario esté logueado
    const usuarioActualStorage = localStorage.getItem('usuarioActual');

    // Parsear los datos del localStorage
    const datosUsuario = JSON.parse(usuarioActualStorage);

    // Extraer cada dato en variables separadas:
    const idUsuario = datosUsuario.idUsuario;                          
    const tipoUsuario = datosUsuario.tipoUsuario;                    
 
    // Mostrar en consola para verificar
    console.log('ID del usuario:', idUsuario);
    console.log('Tipo de usuario:', tipoUsuario);
})