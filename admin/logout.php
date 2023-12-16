<?php

    session_start(); //Incio de la session
    session_unset(); //Desarma el dato
    session_destroy(); //Destruye la session

    header('Location: index.php');

    exit();