<?php 
    include 'connect.php';

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

    if(!isset($noNavbar)){
        include $tpl . 'navbar.php';
    }