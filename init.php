<?php 
    //Reporta Error
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
    
    include 'admin/connect.php';

    $sessionUser = '';
    if(isset($_SESSION['user'])){
        $sessionUser = $_SESSION['user'];
    }

    //Rutas
    $tpl = 'includes/templates/'; //Direccion de templates
    $lang = 'includes/languages/'; //Dir lenguajs
    $func = 'includes/functions/'; //Dir Js
    $css = 'layout/css/'; //Dir css
    $js = 'layout/js/'; //Dir Js

    


    //Include el archivo importante
    include $func . 'funtions.php';
    include $lang . 'inglish.php';
    include $tpl . 'header.php'; 
