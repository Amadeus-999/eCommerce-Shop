<?php
    ob_start();
    session_start();
    $pageTitle = 'Perfil';
    include 'init.php';

    if(isset($_SESSION['user'])){

        $getUser = $con->prepare("SELECT * FROM users WHERE Username = ?");
        $getUser->execute(array($sessionUser));
        $info = $getUser->fetch();
        $userid = $info['UserID']
    ?>
        <h1 class="text-center">Mi Perfil</h1>
        <div class="information block">
            <div class="container">
                <div class="panel panel-primary">
                    <div class="panel-heading">Mi informacion</div>
                    <div class="panel-body">
                        <ul class="list-unstyled">
                            <li>
                                <i class="fa fa-unlock-alt fa-fw"></i>
                                <span>Username</span>: <?php echo $info['Username'] ?>
                            </li>
                            <li>
                                <i class="fa fa-envelope-o fa-fw"></i>
                                <span>Correo</span>: <?php echo $info['Email'] ?>
                            </li>
                            <li>
                                <i class="fa fa-user fa-fw"></i>
                                <span>Nombre Completo</span>: <?php echo $info['FullName'] ?>
                            </li>
                            <li>
                                <i class="fa fa-calendar fa-fw"></i>
                                <span>Dato de Registro</span>: <?php echo $info['Date'] ?>
                            </li>
                            <li>
                                <i class="fa fa-tags fa-fw"></i>
                                <span>Categoria Favorita </span>: 
                            </li>
                        </ul>
                        <a href="#" class="btn btn-default">Editar Informacion</a>
                    </div>
                </div>
            </div>
        </div>
        <div id="my-ads" class="my-ads block">
            <div class="container">
                <div class="panel panel-primary">
                    <div class="panel-heading">Mis Items</div>
                    <div class="panel-body">
                        <?php
                            $myItems = getAllFrom("*", "items", "Item_ID", "DESC", "where Member_ID = $userid", "");
                            if(! empty($myItems)){
                                echo '<div class="row">';
                                    foreach($myItems as $item){
                                        echo '<div class="col-sm-6 col-md-3">';
                                            echo '<div class="thumbnail item-box">';
                                                if($item['Approve'] == 0){ 
                                                    echo '<span class="approve-status"> Esperando aprovacion</span>';
                                                }
                                                echo '<span class="price-tag">$' . $item['Price'] . '</span>';
                                                echo '<img class="item-image" src="admin/uploads/items/' . $item['Image'] . '" alt="" />';
                                                echo '<div class="caption">';
                                                    echo '<h3><a href="items.php?itemid=' . $item['Item_ID'] .'">' . $item['Name'] . '</a></h3>';
                                                    echo '<p class="item-description">' . $item['Description'] . '</p>';
                                                    echo '<div class="date">' . $item['Add_Date'] . '</div>';
                                                echo '</div>'; 
                                            echo '</div>';
                                        echo '</div>';
                                    }
                                echo '</div>';
                            }else{
                                echo 'No hay anuncios para mostrar, crea un <a href="newad.php">Nuevo Anuncio</a>';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="my-comments block">
            <div class="container">
                <div class="panel panel-primary">
                    <div class="panel-heading">Ultimos comentarios</div>
                    <div class="panel-body">
                        <?php
                            $myComments = getAllFrom("comment", "comments", "c_id", "DESC", "where user_id = $userid", "");
                            
                            if(! empty($myComments)){
                                foreach($myComments as $comment){
                                    echo '<p>' . $comment['comment'] . '</p>';
                                }
                            }else{
                                echo 'No hay comentarios para mostrar';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }else{
        header('Location: login.php');
        exit();
    }

    include $tpl . 'footer.php';
    ob_end_flush();
?>