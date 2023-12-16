<?php
    /*
    **Administrador de comentarios
    **Se podra Add| Edit | Delete | Aprovar Comentarios desde aqui|
    */
    ob_start(); //inicio del output buffering 
    session_start();

    $pageTitle = 'Comentarios';
    
    if(isset($_SESSION['Username'])){
        include 'init.php';


        $do = isset($_GET['do']) ? $_GET['do']: 'Manage';

        //Inicio de Adm Page
        if($do == 'Manage'){//Administarcion Miembros Page
            
            //Selecciona todos los usaurios excepto el ADM
            $stmt = $con->prepare("SELECT
                                        comments.*, items.Name AS Item_Name, users.Username AS Member   
                                    FROM
                                        comments
                                    INNER JOIN
                                        items
                                    ON
                                        items.Item_ID = comments.item_id
                                    INNER JOIN
                                        users
                                    ON
                                        users.UserID = comments.user_id
                                    ORDER BY
                                        c_id DESC");
            //Executa accion
            $stmt->execute();
            //Asigna las variables
            $comments = $stmt->fetchAll();
            if(! empty($comments)){
            ?>
                <h1 class="text-center">Administracion de Comentarios</h1>
                <div class="container">
                    <div class="table-responsive">
                        <table class="main-table text-center table table-bordered">
                            <tr>
                                <td>ID</td>
                                <td>Comentario</td>
                                <td>Nombre del item</td>
                                <td>Nombre de usuario</td>
                                <td>Dato de registro</td>
                                <td>Control</td>
                            </tr>
                            
                            <?php
                                foreach($comments as $comment){
                                    echo "<tr>";
                                        echo "<td>" . $comment['c_id'] . "</td>";
                                        echo "<td>" . $comment['comment'] . "</td>";
                                        echo "<td>" . $comment['Item_Name'] . "</td>";
                                        echo "<td>" . $comment['Member'] . "</td>";
                                        echo "<td>" . $comment['comment_date'] . "</td>";
                                        echo "<td>
                                                <a href='comments.php?do=Edit&comid=" . $comment['c_id'] . "'  
                                                    class='btn btn-success'><i class='fa fa-edit'></i> Editar </a>
                                                <a href='comments.php?do=Delete&comid=" . $comment['c_id'] . "''
                                                    class='btn btn-danger confirm'><i class='fa fa-close'></i> Eliminar </a>";
                                                if($comment['status'] == 0){
                                                    echo "<a href='comments.php?do=Approve&comid=" . $comment['c_id'] . "'' 
                                                    class='btn btn-info activate'>
                                                    <i class='fa fa-check'></i> Aprovar</a>";
                                                }
                                        echo "</td>";
                                    echo "</tr>";
                                }
                            ?>
                        </table>
                    </div>
                </div>
                <?php }else{
                        echo '<div class="container">';
                            echo '<div class="nice-message"> No hay comentarios que mostrar </div>';
                        echo '</div>';
                    }?>
            <?php
        
        }elseif($do == 'Edit'){//Edit Page
            //Comprueba si el ID del Request es numerico y obtinen su valor 
            $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0;
            //Selecciona todos los datos dependiendo de la ID
            $stmt = $con->prepare("SELECT * FROM  comments  WHERE c_id = ?");
            //Executa la Query
            $stmt->execute(array($comid));
            //Busca el dato
            $row = $stmt->fetch();
            //El recorrido de filas
            $count = $stmt->rowCount();
            //Si el ID existe muestra los datos en el form
            if($stmt->rowCount() > 0){
                ?>    
                    <h1 class="text-center">Editar Commentario</h1>

                    <div class="container">
                        <form class="form-horizontal" action="?do=Update" method="POST">
                            <input type="hidden" name="comid" value="<?php echo $comid ?>" />
                            <!-- Star comentarios Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Commentario</label> 
                                <div class="col-sm-10 col-md-4">
                                    <textarea class="form-control" name="comment"><?php echo $row['comment'] ?></textarea>
                                </div>
                            </div>
                            <!-- End comentarios Field -->
                            
                            <!-- Star Submit Field -->
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                        <input type="submit"  value="Guardar" class="btn btn-primary btn-lg"/>
                                </div>
                            </div>
                            <!-- End Submit Field -->
                        </form>
                    </div>
                <?php
                //Si no existe tal ID muestra Message Error
                }else{
                    echo "<div class='container'>";
                        $theMsg = '<div class="alert alert-danger">No existe tal ID </div>';
                        redirectHome($theMsg);
                    echo "</div>";
                }
            }elseif($do == 'Update'){
                echo "<h1 class='text-center'>Actualizar Commentario</h1>";
                echo "<div class='container'>";

                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    //Get variables del Form
                    $comid = $_POST['comid'];
                    $comment = $_POST['comment'];
                    
                    //Update la Database con esta info
                    $stmt = $con->prepare("UPDATE comments SET comment = ? WHERE  c_id = ?");
                    $stmt->execute(array($comment, $comid));

                    //Echo Message Exitoso
                    $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Registro Actualizado </div>';
                
                    redirectHome($theMsg, 'back');
                        
                }else{

                    $theMsg = '<div class="alert alert-danger">Lo sentimos, no puedes navegar por esta pagina directamente </div>';
                    redirectHome($theMsg, 'back');
                }
                echo "</div>";
            }elseif($do == 'Delete'){
                //Page Eliminar comentario
                echo "<h1 class='text-center'>Comentario Eliminado</h1>";
                echo "<div class='container'>";

                //Comprueba si el ID del Request es numerico y obtinen su valor 
                $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0;

                //Selecciona todos los datos dependiendo de la ID
                $check = checkItem('c_id', 'comments', $comid);
                
                //Si el ID existe muestra los datos en el form
                if($check > 0){

                    $stmt = $con->prepare("DELETE FROM comments WHERE c_id = :zid");
                    $stmt->bindParam(":zid", $comid);
                    $stmt->execute();

                    //mensaje de confirmacion
                    $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Registro Eliminado </div>';
                    redirectHome($theMsg, 'back');

                }else{
                    $theMsg = '<div class="alert alert-danger">Esta ID no existe </div>';
                    redirectHome($theMsg, 'back');
                }
                echo "</div>";
            }elseif($do == 'Approve'){
                //Page aprovar comentario
                echo "<h1 class='text-center'>Comentario Aprovado</h1>";
                echo "<div class='container'>";

                //Comprueba si el ID del Request es numerico y obtinen su valor 
                $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0;

                //Selecciona todos los datos dependiendo de la ID
                $check = checkItem('c_id', 'comments', $comid);
                
                //Si el ID existe muestra los datos en el form
                if($check > 0){

                    $stmt = $con->prepare("UPDATE comments SET status = 1 WHERE c_id = ?");
                    $stmt->execute(array($comid));

                    //mensaje de confirmacion
                    $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Registro Aprovado </div>';
                    redirectHome($theMsg, 'back');

                }else{
                    $theMsg = '<div class="alert alert-danger">Esta ID no existe </div>';
                    redirectHome($theMsg, 'back');
                }
                echo "</div>";
            }


        include $tpl . 'footer.php';
    }
    else{        
        header('Location: index.php');
        exit();
    }

    ob_end_flush();
?>