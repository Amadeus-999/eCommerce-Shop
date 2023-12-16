<?php
    /*
        Categorias => [Administra | Editra | Update | Insert | Delete | Stats]
    */
    // Condicion ? True : False : 
    $do = isset($_GET['do']) ? $_GET['do']: 'Manage';
    
    //Si la pagina esta en la principal
    if($do == 'Manage'){
        echo 'Bienvenido, estas en la pagina de Administar Categoria';
        echo '<a href="?do=add">Agregar nueva Categoria</a>';
    }elseif($do == 'add'){
        echo 'Bienvenido, estas en la pagina Agregar Categoria';
    }elseif($do == 'insert'){
        echo 'Bienvenido, estas en la pagina insertar Categoria';
    }
    else{
        echo 'Error no hay pagina con este nombre';
    }