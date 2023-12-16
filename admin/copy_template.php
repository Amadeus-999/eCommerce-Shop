<?php
    /**
     * 
     * Pagina de Temples
     */
    
    ob_start();

    session_start();
    $pageTitle = '';

    if(isset($_SESSION['Username'])){
        include 'init.php';
        $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

        if($do == 'Manage'){
            echo 'Welcome';
        }elseif($do == 'add'){

        }elseif($do == 'insert'){

        }elseif($do == 'Edit'){

        }elseif($do == 'Update'){

        }elseif($do == 'Delete'){

        }elseif($do == 'Activate'){

        }
        include $tpl . 'footer.php';
    }else{
        header('Location: index.php');
        exit();
    }
    ob_end_flush();
?>
