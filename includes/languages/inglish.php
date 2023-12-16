<?php
   function lang($phrase){
    static $lang = array(
        /*'MESSAGE' => 'Welcome',
        'ADMIN' => 'Administrator'*/
        //Navbar links
        'HOME_ADMIN' => 'Home',
        'CATEGORIES' => 'Categorias',
        'ITEMS' => 'Items',
        'MEMBERS' => 'Miembros',
        'COMMENTS' => 'Comentarios',
        'STATISTICS' => 'Estadisticas',
        'LOGS' => 'Logs',
        '' => '',
        '' => ''
    );
    return $lang[$phrase];
   }