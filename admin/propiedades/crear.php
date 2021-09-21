<?php
require '../../includes/funciones.php';

$auth = estaAutenticado();

if(!$auth){
    header('Location: /');
}

//Base de datos
require '../../includes/config/database.php';
$db = conectarDB();

//Consultar para obtener vendedores
$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);


//Arreglo con mensajes de error
$errores = [];

$titulo = '';
$precio = '';
$descripcion = '';
$habitaciones = '';
$wc = '';
$estacionamiento = '';
$vendedorId = '';

//Ejecutar el codigo despues de que el usuario envia el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // echo "<pre>";
    // var_dump($_POST);
    // echo "</pre>";

    //  echo "<pre>";
    //  var_dump($_FILES);
    //  echo "</pre>";

    $titulo = mysqli_real_escape_string($db, $_POST['titulo']);
    $precio = mysqli_real_escape_string($db, $_POST['precio']);
    $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);
    $habitaciones = mysqli_real_escape_string($db, $_POST['habitaciones']);
    $wc = mysqli_real_escape_string($db, $_POST['wc']);
    $estacionamiento = mysqli_real_escape_string($db, $_POST['estacionamiento']);
    $vendedorId = mysqli_real_escape_string($db, $_POST['vendedor']);
    $creado = date('Y/m/d');

    //Asignar files hacia una variable
    $imagen = $_FILES['imagen'];

    if (!$titulo) {
        $errores[] = 'Debes añadir un Titulo';
    }
    if (!$precio) {
        $errores[] = 'El Precio es Obligatorio';
    }
    if (strlen($descripcion) < 50) {
        $errores[] = 'La Descripción es obligatoria y debe tener al menos 50 caracteres';
    }
    if (!$habitaciones) {
        $errores[] = 'La Cantidad de Habitaciones es obligatoria';
    }
    if (!$wc) {
        $errores[] = 'La cantidad de WC es obligatoria';
    }
    if (!$estacionamiento) {
        $errores[] = 'La cantidad de lugares de estacionamiento es obligatoria';
    }
    if (!$vendedorId) {
        $errores[] = 'Elige un vendedor';
    }
    if(!$imagen['name'] || $imagen['error']){
        $errores[] = 'La imagen es obligatoria';
    }


    //Validar por tamaño
    $medida = 5000*100;

    if($imagen['size']>$medida){
        $errores[] ='La imagen es muy grande';
    }

    // echo "<pre>";
    // var_dump($errores);
    // echo "</pre>";

    //Revisar que el array de errores esté vacio
    if (empty($errores)) {

        /* Subida de Archivos */
        // Crear carpeta
    $carpetaImagenes = '../../imagenes/';
 
    if (!is_dir($carpetaImagenes)) {
        mkdir($carpetaImagenes);
    }

    // Generar nombre único ** Aquí fue donde encontré el problema
    $nombreImagen = md5(uniqid(rand(), true));
    
    // Subir imagen
    move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);
    
    
    // Insertar datos
    $query = "INSERT INTO propiedades (titulo, precio, imagen, descripcion, habitaciones, wc, estacionamiento, creado, vendedorId) 
    VALUES ('$titulo', '$precio', '$nombreImagen', '$descripcion', '$habitaciones', '$wc', '$estacionamiento', '$creado', '$vendedorId')";

        //  echo $query;

        $resultado = mysqli_query($db, $query);

        if ($resultado) {
            //Redireccionar al usuario.

            header('Location: /admin?resultado=1');
        }
    }
}

incluirTemplate('header');
?>


<main class="contenedor seccion">
    <h1>Crear</h1>

    <a href="/admin" class="boton boton-verde">Volver</a>

    <?php foreach($errores as $error):?>
    <div class="alerta error">
        <?php echo $error;?>
    </div>
    <?php endforeach; ?>

    <form method="POST" action="/admin/propiedades/crear.php" class="formulario" enctype="multipart/form-data">
        <fieldset>
            <legend>Informacion general</legend>

            <label for="titulo">Titulo:</label>
            <input type="text" id="titulo" name="titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo; ?>">

            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" placeholder="Precio Propiedad" value="<?php echo $precio; ?>">

            <label for="imagen">Imagen:</label>
            <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">

            <label for="descripcion">Descripcion:</label>
            <textarea id="descripcion" name="descripcion"><?php echo $descripcion; ?></textarea>
        </fieldset>

        <fieldset>
            <legend>Informacion de la propiedad</legend>

            <label for="habitaciones">Habitaciones:</label>
            <input type="number" 
            id="habitaciones" 
            name="habitaciones" 
            placeholder="Ej: 3" 
            min="1" max="9" 
            value="<?php echo $habitaciones; ?>">

            <label for="wc">Baños:</label>
            <input type="number" 
            id="wc" name="wc"
            placeholder="Ej: 3" 
            min="1" max="9" 
            value="<?php echo $wc; ?>">

            <label for="estacionamiento">Estacionamiento:</label>
            <input type="number" 
            id="estacionamiento" 
            name="estacionamiento" 
            placeholder="Ej: 3" 
            min="1" max="9" 
            value="<?php echo $estacionamiento; ?>">


        </fieldset>

        <fieldset>
            <legend>Vendedor</legend>

            <select name="vendedor">
                <option value="">--Seleccione--</option>
                <?php while($vendedor = mysqli_fetch_assoc($resultado)):?>
                    <option <?php echo $vendedorId === $vendedor['id'] ? 'selected' :'';  ?>   value="<?php echo $vendedor['id'];?>"> <?php echo $vendedor['nombre'] . " " . $vendedor['apellido'];  ?></option>
                <?php endwhile; ?>
            </select>

        </fieldset>

        <input type="submit" value="Crear propiedad" class="boton boton-verde">

    </form>
</main>

<?php
incluirTemplate('footer');
?>