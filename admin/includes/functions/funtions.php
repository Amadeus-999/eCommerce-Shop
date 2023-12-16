<?php


    /*
    **Get All funcion
    **Funcion para obetener todos los registros de cualquier tabala de Datbase
    */
    function getAllFrom($field, $table, $orderfield, $ordering = 'DESC', $where = NULL, $and = NULL) {
        global $con;
    
        $getAll = $con->prepare("SELECT $field FROM $table $where $and ORDER BY $orderfield $ordering");
        $getAll->execute();
        $all = $getAll->fetchAll();
        return $all;
    }


    
    /*
    **Funcion de titulo echo para la pagina en caso de que la pagina
    ** Tenga variables $pageTitle y el titulo echo default para otras paginas
    */
    function getTitle(){
        global $pageTitle;

        if(isset($pageTitle)){
            echo $pageTitle;
        }else{
            echo 'Default';
        }
    }

    /*
    ** Funcion para redireccionar a Home [Acepta parametros: ]
    **$theMsg = Echo el mensaje de error [Error | Success | Warning]
    **$url = EL link al que sera redirigido
    **$seconds = Segundos antes de refirigir
    */
    function redirectHome($theMsg, $url = null, $seconds = 3){
        if($url === null){
            $url = 'index.php';
            $link = 'Pagina de inicio';
        }else{
            if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != ''){
                $url = $_SERVER['HTTP_REFERER'];
                $link = 'Pagina previa';
            }else{
                $url = $url = 'index.php';
                $link = 'Pagina de inicio';
            }
        }
        echo $theMsg;

        echo "<div class='alert alert-info'> Seras redireccionado a $link despues de $seconds Segundos</div>";

        header("refresh:$seconds;url=$url");
        exit();
    }


    /*
    **Funcion para verificar los Items de la Database [Acepta los parametros: ]
    **$select = El elemento a seleccionar [Ejem: user, item, categoria]
    **$from = La tabla a seleccionar [Ejem: users, items. categorias]
    **
    **
    */
    function checkItem($select, $from, $value){
        global $con;

        $statement = $con->prepare("SELECT $select FROM $from WHERE $select = ?");
        $statement->execute(array($value));
        $count = $statement->rowCount();
        return $count;
    }


    /**
     * *Funcion que cuenta el numero de items
     * *Funcion que cuenta el numero de filas de items
     * *$item = el articulo a contar
     * *$table = la tabla a elegir
     */
    function countItems($item, $table){
        global $con;
        $stm2 = $con->prepare("SELECT COUNT($item) FROM $table");
        $stm2->execute();
        echo $stm2->fetchColumn();
    }
    
    /*
    **Funcion para obtener los registros mas recientes
    **Funcion para tener los Items mas recientes de la Database [Users, Items, Comentarios]
    **$select = Selecciona el campo
    **$table = La tabla a elegir
    **$order = Ordena DESC
    **$limit = Numero de registros a obtener
    */
    function getLastest($select, $table, $order,$limit = 5){
        global $con;
        $getStmt = $con->prepare("SELECT $select FROM $table ORDER BY $order DESC LIMIT $limit");
        $getStmt->execute();
        $row = $getStmt->fetchAll();
        return $row;
    }