<?php
    ob_start();

    session_start();
    $pageTitle = 'Categorias';

    if(isset($_SESSION['Username'])){
        include 'init.php';
        $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

        if($do == 'Manage'){
            $sort = 'asc';
            $sort_array = array('asc', 'desc');
            if(isset($_GET['sort']) && in_array($_GET['sort'], $sort_array)){
                $sort = $_GET['sort'];
            }

            $stmt2 = $con->prepare("SELECT * FROM categories WHERE parent = 0 ORDER BY Ordening $sort");
            $stmt2->execute();
            $cats = $stmt2->fetchAll();

            if(! empty($cats)){
            ?>
                <h1 class="text-center">Administracion de Categorias</h1>
                <div class="container categories">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-edit"></i> Admistracion de categorias
                            <div class="option pull-right">
                                <i class="fa fa-sort"></i> Orden:[
                                <a class="<?php if ($sort == 'asc'){ echo 'active'; } ?>" href="?sort=asc">Asc</a> |
                                <a class="<?php if ($sort == 'desc'){ echo 'active'; } ?>" href="?sort=desc">Desc</a>]
                                <i class="fa fa-eye"></i> Ver:[
                                <span class="active" data-view="full">Full</span> |
                                <span data-view="classic">Clasico</span>]
                            </div>
                        </div>
                        <div class="panel-body">
                            <?php
                                foreach($cats as $cat){
                                    echo "<div class='cat'>";
                                        echo "<div class='hidden-buttons'>";
                                            echo "<a href='categorias.php?do=Edit&catid=" . $cat['ID'] . "' class='btn btn-xs btn-primary'><i class='fa fa-edit'></i> Editar</a>";
                                            echo "<a href='categorias.php?do=Delete&catid=" . $cat['ID'] . "' class='confirm btn btn-xs btn-danger'><i class='fa fa-close'></i> Eliminar</a>";
                                        echo "</div>";
                                        echo "<h3>" . $cat['Name'] . '</h3>';
                                        echo "<div class='full-view'>";
                                            echo "<p>"; if($cat['Description'] == ''){echo 'Esta categoria no tiene descripcion';}else{ echo $cat['Description'];} echo "</p>";
                                            if($cat['Visibility'] == 1){echo '<span class="visibility cat-span"><i class="fa fa-eye"></i> Oculta</span>'; }
                                            if($cat['Allow_Comment'] == 1){echo '<span class="commenting"><i class="fa fa-close"></i> Comentario Desactivado</span>'; }
                                            if($cat['Allow_Ads'] == 1){echo '<span class="anuncio"><i class="fa fa-close"></i> Anuncio Desactivado</span>'; }
                                        echo "</div>";
                                        //Toma los hijos de las categorias
                                        $childCats = getAllFrom("*", "categories", "ID", "ASC", "where parent = {$cat['ID']}", "");
                                        if(! empty($childCats)) {   
                                            echo "<h4 class='child-head'>Hijos de las categorias </h4>";
                                            echo "<ul class='list-unstyled child-cats'>";
                                                foreach($childCats as $c){
                                                    echo "<li class='child-link'>
                                                        <a href='categorias.php?do=Edit&catid=" . $c['ID'] . "'>" . $c['Name'] . "</a>
                                                        <a href='categorias.php?do=Delete&catid=" . $c['ID'] . "' class='show-delete confirm'> Eliminar</a>
                                                    </li>";
                                                }
                                            echo "</ul>";
                                        }
                                    echo "</div>";
                                    echo "<hr>";
                                }
                            ?>
                        </div>
                    </div>
                    <a class="add-category btn btn-primary" href="categorias.php?do=add">
                        <i class="fa fa-plus"></i> Agregar nueva cateogria</a>
                </div>
                <?php }else{
                        echo '<div class="container">';
                            echo '<div class="nice-message"> No hay categorias que mostrar </div>';
                            echo  '<a class="add-category btn btn-primary" href="categorias.php?do=add">
                                        <i class="fa fa-plus"></i> Agregar nueva cateogria
                                    </a>';
                        echo '</div>';
                    }?>
            <?php

        }elseif($do == 'add'){
            ?>
                <h1 class="text-center">Agregar nueva categoria</h1>
                <div class="container">
                    <form class="form-horizontal" action="?do=insert" method="POST">
                        <!-- Star Name Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Nombre</label> 
                            <div class="col-sm-10 col-md-6">
                                    <input type="text" name="name" class="form-control" autocomplete="off" required="required" placeholder="Nombre de la categoria"/>
                            </div>
                        </div>
                        <!-- End Name Field -->
                        <!-- Star Descripcion Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Descripcion</label> 
                            <div class="col-sm-10 col-md-6">
                                <input type="text"  name="description" class="form-control" placeholder="Describe la categoria"/>
                            </div>
                        </div>
                        <!-- End Descripcion Field -->
                        <!-- Star ordenar Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Ordenar</label> 
                            <div class="col-sm-10 col-md-6">
                                    <input type="text"  name="ordering" class="form-control" placeholder="Numero para organizar tu categoria"/>
                            </div>
                        </div>
                        <!-- End ordenar Field -->
                        <!-- Start Categoria type -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Tipo de categoria</label> 
                            <div class="col-sm-10 col-md-6">
                                <select name="parent">
                                    <option value="0">Nada</option>
                                    <?php
                                        $allCats = getAllFrom("*", "categories", "ID", "ASC", "WHERE parent = 0", "");
                                        foreach($allCats as $cat){
                                            echo "<option value='" . $cat['ID'] . "'>" . $cat['Name'] . "</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- End categoria type -->
                        <!-- Star Visibilidad Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Visible</label> 
                            <div class="col-sm-10 col-md-6">
                                    <div>
                                        <input id="vis-yes" type="radio" name="visibility" value="0" checked />
                                        <label for="vis-yes">Si</label>
                                    </div>
                                    <div>
                                        <input id="vis-no" type="radio" name="visibility" value="1"/>
                                        <label for="vis-no">No</label>
                                    </div>
                            </div>
                        </div>
                        <!-- End Visibilidad Field -->
                        <!-- Star Comentarios Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Permitir Comentar</label> 
                            <div class="col-sm-10 col-md-6">
                                    <div>
                                        <input id="com-yes" type="radio" name="commenting" value="0" checked />
                                        <label for="com-yes">Si</label>
                                    </div>
                                    <div>
                                        <input id="com-no" type="radio" name="commenting" value="1"/>
                                        <label for="com -no">No</label>
                                    </div>
                            </div>
                        </div>
                        <!-- End Comentarios Field -->
                        <!-- Star Ads Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Permitir Anuncios</label> 
                            <div class="col-sm-10 col-md-6">
                                    <div>
                                        <input id="Ads-yes" type="radio" name="ads" value="0" checked />
                                        <label for="Ads-yes">Si</label>
                                    </div>
                                    <div>
                                        <input id="Ads-no" type="radio" name="ads" value="1"/>
                                        <label for="Ads -no">No</label>
                                    </div>
                            </div>
                        </div>
                        <!-- End Ads Field -->
                        <!-- Star Submit Field -->
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                    <input type="submit"  value="Agregar Categoria" class="btn btn-primary btn-lg"/>
                            </div>
                        </div>
                        <!-- End Submit Field -->
                    </form>
                </div>
            <?php
        }elseif($do == 'insert'){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){

                echo "<h1 class='text-center'>Insertar Categoria</h1>";
                echo "<div class='container'>";

                //Get variables del Form
                $name = $_POST['name'];
                $desc = $_POST['description'];
                $parent = $_POST['parent'];
                $order = $_POST['ordering'];
                $visible = $_POST['visibility'];
                $comment = $_POST['commenting'];
                $ads = $_POST['ads'];
                
                //Checa si la categoria existe en la Database
                $check = checkItem("Name", "categories", $name);

                if($check == 1){
                    $theMsg = "<div class='alert alert-danger'>" . ' Perdon, este categoria ya existe</div>';
                    redirectHome($theMsg, 'back');
                } else{
                    //Insertar informacion de usuario en Database
                    $stmt = $con->prepare("INSERT INTO
                                            categories(Name, Description, parent, Ordening, Visibility, Allow_Comment, Allow_Ads)
                                            VALUES(:zname, :zdesc, :zparent, :zorder, :zvisible, :zcomment, :zads)");
                    $stmt->execute(array(
                        'zname' => $name,
                        'zdesc' => $desc,
                        'zparent' => $parent,
                        'zorder' => $order,
                        'zvisible' => $visible,
                        'zcomment' => $comment,
                        'zads' => $ads
                    ));
                    //Echo Message Exitoso
                        $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Registro Insertado </div>';
                        redirectHome($theMsg, 'back');
                }
            }else{
                echo "<div class='container'>";
                    $theMsg ='<div class="alert alert-danger"> Lo sentimos, no puedes navegar por esta pagina directamente</div>';
                    redirectHome($theMsg, 'back');
                echo "</div>";
            }
            echo "</div>";

        }elseif($do == 'Edit'){
            //Comprueba si el cateid del Request es numerico y obtinen su valor 
            $catid= isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;
            //Selecciona todos los datos dependiendo de la ID
            $stmt = $con->prepare("SELECT * FROM  categories  WHERE ID = ?");
            //Executa la Query
            $stmt->execute(array($catid));
            //Busca el dato
            $cat = $stmt->fetch();
            //El recorrido de filas
            $count = $stmt->rowCount();
            //Si el ID existe muestra los datos en el form
            if($stmt->rowCount() > 0){
                ?>    
                    <h1 class="text-center">Editar Categoria</h1>
                    <div class="container">
                    <form class="form-horizontal" action="?do=Update" method="POST">
                        <!-- Star Name Field -->
                        <input type="hidden" name="catid" value="<?php echo $catid ?>" />
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Nombre</label> 
                            <div class="col-sm-10 col-md-6">
                                    <input type="text" name="name" class="form-control" required="required" placeholder="Nombre de la categoria" value="<?php echo $cat['Name'] ?>"/>
                            </div>
                        </div>
                        <!-- End Name Field -->
                        <!-- Star Descripcion Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Descripcion</label> 
                            <div class="col-sm-10 col-md-6">
                                <input type="text"  name="description" class="form-control" placeholder="Describe la categoria" value="<?php echo $cat['Description'] ?>"/>
                            </div>
                        </div>
                        <!-- End Descripcion Field -->
                        <!-- Star ordenar Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Ordenar</label> 
                            <div class="col-sm-10 col-md-6">
                                    <input type="text"  name="ordering" class="form-control" placeholder="Numero para organizar tu categoria" value="<?php echo $cat['Ordening'] ?>"/>
                            </div>
                        </div>
                        <!-- End ordenar Field -->
                        <!-- Start Categoria type -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Tipo de categoria</label> 
                            <div class="col-sm-10 col-md-6">
                                <select name="parent">
                                    <option value="0">Nada</option>
                                    <?php
                                        $allCats = getAllFrom("*", "categories", "ID", "ASC", "WHERE parent = 0", "");
                                        foreach($allCats as $c){
                                            echo "<option value='" . $c['ID'] . "'" ;
                                            if ($cat['parent'] == $c['ID']){ echo 'selected';}
                                            echo ">" . $c['Name'] . "</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- End categoria type -->
                        <!-- Star Visibilidad Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Visible</label> 
                            <div class="col-sm-10 col-md-6">
                                    <div>
                                        <input id="vis-yes" type="radio" name="visibility" value="0" <?php if($cat['Visibility'] == 0){ echo 'checked';} ?> />
                                        <label for="vis-yes">Si</label>
                                    </div>
                                    <div>
                                        <input id="vis-no" type="radio" name="visibility" value="1" <?php if($cat['Visibility'] == 1){ echo 'checked';} ?>/>
                                        <label for="vis-no">No</label>
                                    </div>
                            </div>
                        </div>
                        <!-- End Visibilidad Field -->
                        <!-- Star Comentarios Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Permitir Comentar</label> 
                            <div class="col-sm-10 col-md-6">
                                    <div>
                                        <input id="com-yes" type="radio" name="commenting" value="0" <?php if($cat['Allow_Comment'] == 0){ echo 'checked';} ?>/>
                                        <label for="com-yes">Si</label>
                                    </div>
                                    <div>
                                        <input id="com-no" type="radio" name="commenting" value="1" <?php if($cat['Allow_Comment'] == 1){ echo 'checked';} ?>/>
                                        <label for="com -no">No</label>
                                    </div>
                            </div>
                        </div>
                        <!-- End Comentarios Field -->
                        <!-- Star Ads Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Permitir Anuncios</label> 
                            <div class="col-sm-10 col-md-6">
                                    <div>
                                        <input id="Ads-yes" type="radio" name="ads" value="0" <?php if($cat['Allow_Ads'] == 0){ echo 'checked';} ?>/>
                                        <label for="Ads-yes">Si</label>
                                    </div>
                                    <div>
                                        <input id="Ads-no" type="radio" name="ads" value="1" <?php if($cat['Allow_Ads'] == 1){ echo 'checked';} ?>/>
                                        <label for="Ads -no">No</label>
                                    </div>
                            </div>
                        </div>
                        <!-- End Ads Field -->
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
            echo "<h1 class='text-center'>Actualizar Categoria</h1>";
                echo "<div class='container'>";

                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    //Get variables del Form
                    $id = $_POST['catid'];
                    $name = $_POST['name'];
                    $desc= $_POST['description'];
                    $order = $_POST['ordering'];
                    $parent = $_POST['parent'];
                    $visible = $_POST['visibility'];
                    $comment = $_POST['commenting'];
                    $ads = $_POST['ads'];


                    
                    //Update la Database con esta info
                    $stmt = $con->prepare("UPDATE 
                                            categories 
                                        SET 
                                            Name = ?, 
                                            Description = ?, 
                                            Ordening = ?, 
                                            parent= ?,
                                            Visibility = ?,
                                            Allow_Comment = ?, 
                                            Allow_Ads = ? 
                                        WHERE  
                                            ID = ?");
                    $stmt->execute(array($name, $desc, $order, $parent, $visible, $comment, $ads, $id));

                    //Echo Message Exitoso
                    $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Registro Actualizado </div>';
                
                    redirectHome($theMsg, 'back');
                        
                }else{

                    $theMsg = '<div class="alert alert-danger">Lo sentimos, no puedes navegar por esta pagina directamente </div>';
                    redirectHome($theMsg, 'back');
                }
            echo "</div>";

        }elseif($do == 'Delete'){
            //Page Eliminar miembro
            echo "<h1 class='text-center'>Categoria Eliminada</h1>";
            echo "<div class='container'>";

            //Comprueba si el ID del Request es numerico y obtinen su valor 
            $catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;

            //Selecciona todos los datos dependiendo de la ID
            $check = checkItem('ID', 'categories', $catid);
            
            //Si el ID existe muestra los datos en el form
            if($check > 0){

                $stmt = $con->prepare("DELETE FROM categories WHERE ID = :zid");
                $stmt->bindParam(":zid", $catid);
                $stmt->execute();

                //mensaje de confirmacion
                $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Registro Eliminado </div>';
                redirectHome($theMsg, 'back');

            }else{
                $theMsg = '<div class="alert alert-danger">Esta ID no existe </div>';
                redirectHome($theMsg, 'back');
            }
            echo "</div>";
        }
        include $tpl . 'footer.php';
    }else{
        header('Location: index.php');
        exit();
    }
    ob_end_flush();
?>
