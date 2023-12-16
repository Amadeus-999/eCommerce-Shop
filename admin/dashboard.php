<?php

    session_start();
    
    if(isset($_SESSION['Username'])){
        $pageTitle = 'Dashboard';

        include 'init.php';

        /*Pagina Start Dashboard  */
        $numUsers = 4; //Cunatos usuarios
        $latestUsers = getLastest("*", "users", "UserID", $numUsers);//obtiene los utlimos usuarios

        $numItems = 4;
        $lastestItems = getLastest("*", 'items', 'Item_ID', $numItems);//Ultimos items

        $numComments = 3;

        ?>
            <div class="home-stats">
                <div class="container text-center">
                    <h1>Dashboard</h1>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat st-members">
                                <i class="fa fa-users"></i>
                                <div class="info">
                                    Total de miembros
                                    <span><a href="members.php"><?php countItems('UserID', 'users') ?></a></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat st-venta">
                                <i class="fa fa-user-plus"></i>
                                <div class="info">
                                    Miembros de venta
                                    <span><a href="members.php?do=Manage&page=Venta">
                                        <?php echo checkItem("RegStatus", "users", 0) ?>
                                    </a></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat st-items">
                                <i class="fa fa-tag"></i>
                                <div class="info">
                                    Total de items
                                    <span>
                                        <a href="items.php"><?php countItems('Item_ID ', 'items') ?></a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat st-comments">
                                <i class="fa fa-comments"></i>
                                <div class="info">
                                    Total de comentarios
                                    <span>
                                        <a href="comments.php"><?php echo countItems('c_id ', 'comments') ?></a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="latest">
                <div class="container latest">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <i class="fa fa-users"></i> 
                                    Ultimos <?php echo $numUsers ?> Usuarios Registrados
                                    <span class="toogle-info pull-right">
                                        <i class="fa fa-minus fa-lg"></i>
                                    </span>
                                </div>
                                <div class="panel-body">
                                    <ul class="list-unstyled latest-users">
                                        <?php
                                            if(! empty($latestUsers)){
                                                foreach($latestUsers as $user){
                                                    echo '<li>';
                                                        echo $user['Username'];
                                                        echo '<a href="members.php?do=Edit&userid=' . $user['UserID'] . '">';
                                                            echo '<span class="btn btn-success pull-right">';
                                                                echo '<i class="fa fa-edit"></i>Editar';
                                                                if($user['RegStatus'] == 0){
                                                                    echo "<a href='members.php?do=Activate&userid=" . $user['UserID'] . "'' 
                                                                    class='btn btn-info pull-right activate'>
                                                                    <i class='fa fa-check'></i> Activar</a>";
                                                                }
                                                            echo '</span>';
                                                        echo '</a>';
                                                    echo '</li>';
                                                }
                                            }else{
                                                echo 'No hay miembros que mostrar';
                                            }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <i class="fa fa-tag"></i> Ultimos <?php echo $numItems ?> Items
                                    <span class="toogle-info pull-right">
                                        <i class="fa fa-minus fa-lg"></i>
                                    </span>
                                </div>
                                <div class="panel-body">
                                    <ul class="list-unstyled latest-users">
                                        <?php
                                            if(! empty($lastestItems)){
                                                foreach($lastestItems as $item){
                                                    echo '<li>';
                                                        echo $item['Name'];
                                                        echo '<a href="items.php?do=Edit&itemid=' . $user['UserID'] . '">';
                                                            echo '<span class="btn btn-success pull-right">';
                                                                echo '<i class="fa fa-edit"></i>Editar';
                                                                if($item['Approve'] == 0){
                                                                    echo "<a href='items.php?do=Approve&itemid=" . $item['Item_ID'] . "'' 
                                                                    class='btn btn-info pull-right activate'>
                                                                    <i class='fa fa-check'></i> Activar</a>";
                                                                }
                                                            echo '</span>';
                                                        echo '</a>';
                                                    echo '</li>';
                                                }
                                            }else{
                                                echo 'No hay items que mostrar';
                                            }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Comienzo ultimos comentarios -->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <i class="fa fa-comments-o"></i> 
                                    Ultimos <?php echo $numComments ?> comentarios
                                    <span class="toogle-info pull-right">
                                        <i class="fa fa-minus fa-lg"></i>
                                    </span>
                                </div>
                                <div class="panel-body">
                                    <?php
                                        $stmt = $con->prepare("SELECT
                                                                    comments.*, users.Username AS Member   
                                                                FROM
                                                                    comments
                                                                INNER JOIN
                                                                    users
                                                                ON
                                                                    users.UserID = comments.user_id
                                                                ORDER BY 
                                                                    c_id DESC
                                                                LIMIT $numComments");
                                        //Executa accion
                                        $stmt->execute();
                                        //Asigna las variables
                                        $comments = $stmt->fetchAll();
                                        
                                        if(! empty($comments)){
                                            foreach ($comments as $comment){
                                                echo '<div class="comment-box">';
                                                    echo '<span class="member-n">
                                                        <a href="members.php?do=Edit&userid=' . $comment['user_id'] . '">
                                                        '. $comment['Member'] . '</a></span>';
                                                    echo '<p class="member-c">'. $comment['comment'] . '</p>';
                                                echo '</div>';
                                            }
                                        }else{
                                            echo 'No hay comentarios que mostrar';
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Final ultimos comentarios -->
                </div>
            </div>

        <?php
        /*Pagina End Dashboard*/

        include $tpl . 'footer.php';
    }
    else{
        //echo 'Tu no tienes autorizacion para ver esta pagina';
        
        header('Location: index.php');
        exit();
    }