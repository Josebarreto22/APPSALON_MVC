<h1 class="nombre-pagina">Recuperar Password</h1>
<p class="descripcion-pagina">Coloca tu nuevo Password a continuación</p>

<?php 
    include_once __DIR__ . '/../templates/alertas.php'; 
?>

<!-- No mostrar el formulario si el token no es valido -->
<?php if($error) return; ?>
<form class="formulario" method="POST">
        <div class="campo">
            <label for="password">Password:</label>
            <input  type="password" id="password" name="password" placeholder="Tu Nuevo Password" />
        </div>

        <div>
            <input type="submit" class="boton" value="Guardar Nuevo Password">
        </div>
</form>

<div class="acciones">
    <a href="/">¿Ya tienes cuenta? Iniciar Sesión</a>
    <a href="/crear-cuenta">¿Aùn no tienes cuenta? Obtener una</a>
</div>