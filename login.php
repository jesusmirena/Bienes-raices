<?php


require 'includes/config/database.php';
$db = conectarDB();

//Autenticar el usuario

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // echo "<pre>";
    // var_dump($_POST);
    // echo "</pre>";

    $email = mysqli_real_escape_string($db, filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));
    $password = mysqli_real_escape_string($db, $_POST['password']);

    if (!$email) {
        $errores[] = "El email es obligatorio o no es valido";
    }

    if (!$password) {
        $errores[] = "El password es obligatorio";
    }
    if (empty($errores)) {

        //Revisar si el usuario existe 
        $query = "SELECT * FROM usuarios WHERE email = '${email}'";
        $resultado = mysqli_query($db, $query);

        if ($resultado->num_rows) {
            //Revisar si el password es correcto
            $usuario = mysqli_fetch_assoc($resultado);

            //Verificar el password 
            $auth = password_verify($password, $usuario['password']);

            if ($auth) {
                //El usuario está autenticado
                session_start();

                //Llenar el arreglo de sesión
                $_SESSION['usuario'] = $usuario['email'];
                $_SESSION['login'] = true;
                
                //Redireccionar al usuario.
                header('Location: /admin');
            } else {
                $errores[] = "El password es incorrecto.";
            }
        } else {
            $errores[] = "El usuario no existe";
        }
    }
}
//Incluye el header
require 'includes/funciones.php';
incluirTemplate('header');
?>


<main class="contenedor seccion contenido-centrado">
    <h1>Iniciar sesión</h1>


    <?php foreach ($errores as $error) : ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form method="POST" class="formulario">
        <fieldset>
            <legend>Email y Password</legend>

            <label for="email">E-mail</label>
            <input name="email" type="email" placeholder="Tu Email" id="email" required>

            <label for="password">Password</label>
            <input name="password" type="password" placeholder="Tu Password" id="password" required>

            <input type="submit" value="Iniciar Sesión" class="boton boton-verde">
        </fieldset>

    </form>
</main>

<?php
incluirTemplate('footer');
?>