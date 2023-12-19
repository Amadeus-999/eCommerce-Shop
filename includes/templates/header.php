<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php getTitle() ?></title>
    <link rel="stylesheet" href="<?php echo $css; ?>bootstrap.min.css"/>
    <link rel="stylesheet" href="<?php echo $css; ?>font-awesome.min.css"/>
    <link rel="stylesheet" href="<?php echo $css; ?>jquery-ui.css"/>
    <link rel="stylesheet" href="<?php echo $css; ?>jquery.selectBoxIt.css"/>
    <link rel="stylesheet" href="<?php echo $css; ?>front.css"/>
</head>
<body>
    <div class="upper-bar">
        <div class="container">
            <?php
                if(isset($_SESSION['user'])){ ?>

                    <?php
                        $username = $_SESSION['user'];
                        $stmt = $con->prepare("SELECT avatar FROM users WHERE Username = :username");

                        // Asociar el parámetro
                        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                    
                        // Ejecutar la consulta
                        $stmt->execute();
                    
                        // Obtener el resultado
                        $avatar = $stmt->fetchColumn();
                    
                        // Cerrar la consulta
                        $stmt->closeCursor();
                    
                        if ($avatar) {
                            // Mostrar la imagen del avatar con la ruta completa
                            echo '<img class="my-image img-circle" src="admin/uploads/avatars/' . $avatar . '" alt="User Avatar" />';
                        } else {
                            // Mostrar una imagen predeterminada o un mensaje de que no se encontró un avatar
                            echo '<img class="my-image img-thumbnail img-circle" src="ruta_de_imagen_predeterminada.jpg" alt="Default Avatar" />';
                        }
                    ?>

                    
                    <div class="btn-group my-info">
                        <span class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <?php echo $sessionUser ?>
                            <span class="caret"></span>
                        </span>
                        <ul class="dropdown-menu">
                            <li><a href="profile.php">Mi Perfil</a></li>
                            <li><a href="newad.php">Nuevo Item</a></li>
                            <li><a href="profile.php#my-ads">Mis Items</a></li>
                            <li><a href="logout.php">Cerrar Sesion</a></li>
                        </ul>
                    </div>
                    <?php
                    

                                      
                }else{
            ?>
            <a href="login.php">
                <span class="pull-right">Login/Registro</span>
            </a>
            <?php } ?>
        </div>
    </div>
    <nav class="navbar navbar-inverse">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-nav" aria-expanded="false">
                    <span class="sr-only">Toogle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Pagina Principal</a>
            </div>
            <div class="collapse navbar-collapse" id="app-nav">
                <ul class="nav navbar-nav navbar-right">
                    <?php
                        $allCats = getAllFrom("*", "categories", "ID", "ASC", "where parent = 0", "");
                        foreach($allCats as $cat){
                            echo 
                            '<li>
                                <a href="categorias.php?pageid=' . $cat['ID'] .'">
                                    ' . $cat['Name'] . '
                                </a>
                            </li>';
                        }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
