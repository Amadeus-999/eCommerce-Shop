<?php
    ob_start();
    session_start();
    $pageTitle = 'Show Items';
    include 'init.php';

    //Comprueba si el nombre del Request es numerico y obtinen su valor 
    $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
    //Selecciona todos los datos dependiendo de la ID
    $stmt = $con->prepare("SELECT 
                                items.*, 
                                categories.Name AS category_name,
                                users.Username
                            FROM
                                items
                            INNER JOIN
                                categories 
                            ON
                                categories.ID = items.Cat_ID 
                            INNER JOIN
                                users
                            ON
                                users.UserID = items.Member_ID 
                            WHERE
                                Item_ID = ? 
                            AND 
                                Approve = 1");
    //Executa la Query
    $stmt->execute(array($itemid));

    $count = $stmt->rowCount();

    if($count > 0){
    //Busca el dato
    $item = $stmt->fetch();
?>

    <h1 class="text-center"><?php echo $item['Name'] ?></h1>
    
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <?php
                echo '<img src="admin/uploads/items/' . $item['Image'] . '" alt="Imagen del producto" class="product-image" />';
                ?>
            </div>
            <div class="col-md-9 item-info">
                <h2><?php echo $item['Name'] ?></h2>
                <p><?php echo $item['Description'] ?></p>
                <ul class="list-unstyled">
                    <li>
                        <i class="fa fa-calendar fa-fw"></i>
                        <span>Fecha agregada</span>: <?php echo $item['Add_Date'] ?>
                    </li>
                    <li>
                        <i class="fa fa-money fa-fw"></i>
                        <span>Precio</span>: $<?php echo $item['Price'] ?>
                    </li>
                    <li>
                        <i class="fa fa-building fa-fw"></i>
                        <span>Hecho en</span>: <?php echo $item['Country_Made'] ?>
                    </li>
                    <li>
                        <i class="fa fa-tags fa-fw"></i>
                        <span>Categoria</span>: <a href="categorias.php?pageid=<?php echo $item['Cat_ID'] ?>"><?php echo $item['category_name'] ?></a>
                    </li>
                    <li>
                        <i class="fa fa-user fa-fw"></i>
                        <span>Añadido por</span>: <a href="#"><?php echo $item['Username'] ?></a>
                    </li>
                    <li class="tags-items">
                        <i class="fa fa-tags fa-fw"></i>
                        <span>Tags</span>: 
                        <?php 
                            $allTags = explode(",", $item['tags']);
                            foreach($allTags as $tag){
                                $tag = str_replace(' ', '', $tag);
                                $lowertag = strtolower($tag);
                                if(! empty($tag)){
                                    echo "<a href='tags.php?name={$lowertag}'>" . $tag , '</a> | ';
                                }
                            }
                        ?>
                    </li>
                </ul>
            </div>
        </div>
        <hr class="custom-hr">
        <?php
            if(isset($_SESSION['user'])){ ?>
            <!-- Start Add Comment -->
            <div class="row">
                <div class="col-md-offset-3">
                    <div class="add-comment">
                        <h3>Agrega tu comentario</h3>
                        <form action="<?php echo $_SERVER['PHP_SELF'] . '?itemid=' . $item['Item_ID'] ?>" method="POST">
                            <textarea class="form-control" name="comment" required></textarea>
                            <input  class="btn btn-primary" type="submit" value="Agregar comentario">
                        </form>
                        <?php
                            if($_SERVER['REQUEST_METHOD'] == 'POST'){

                                $comment = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);
                                $itemid = $item['Item_ID'];
                                $userid = $_SESSION['uid'];

                                if(! empty($comment)){
                                    $stmt = $con->prepare("INSERT INTO
                                        comments(comment, status, comment_date, item_id, user_id)
                                        VALUES(:zcomment, 0, NOW(), :zitemid, :zuserid)");

                                    $stmt->execute(array(
                                        'zcomment' => $comment,
                                        'zitemid' => $itemid,
                                        'zuserid' => $userid
                                    ));
                                    if($stmt){
                                        echo '<div class="alert alert-success">Comentario publicado</div>';
                                    }
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
            <!-- End Add Comment -->
        <?php }else{
            echo '<a href="login.php">Logeate</a> o <a href="login.php">Registrate</a> para poder agregar comentarios';
        } ?>    
        <hr class="custom-hr">
        <?php
            $stmt = $con->prepare("SELECT
                                    comments.*, users.Username AS Member   
                                FROM
                                    comments
                                INNER JOIN
                                    users
                                ON
                                    users.UserID = comments.user_id
                                WHERE
                                    item_id = ?
                                ORDER BY
                                    c_id DESC");
            $stmt->execute(array($item['Item_ID']));
            $comments = $stmt->fetchAll();

            
        ?>
        <?php
            foreach($comments as $comment){
                ?>
                <div class="comment-box">
                    <div class="row">
                        <div class="col-sm-2 text-center">
                            <img class="img-responsive img-thumbnail img-circle center-block" src="R.png" alt="" />
                            <?php echo $comment['Member'] ?>
                        </div>
                        <div class="col-sm-10">
                            <p class="lead" ><?php echo $comment['comment'] ?></p>
                        </div>
                    </div>
                </div>
                <hr class="custom-hr">
            <?php } ?>
    </div>
        
<?php
    }else{
        echo '<div class="container">';
            echo '<div class="alert alert-danger">Este ID no existe o este item esta esperenado aprovacion</div>';
        echo '</div>';
    }
    include $tpl . 'footer.php';
    ob_end_flush();
?>