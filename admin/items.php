<?php
    
    ob_start();

    session_start();
    $pageTitle = 'Items';

    if(isset($_SESSION['Username'])){
        include 'init.php';

        $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

        if($do == 'Manage'){
            

            $stmt = $con->prepare("SELECT 
                                        items.*, 
                                        categories.Name AS categoria_name, 
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
                                    ORDER BY
                                        Item_ID DESC");
            //Executa accion
            $stmt->execute();
            //Asigna las variables
            $items = $stmt->fetchAll();

            if(! empty($items)){
            ?>
                <h1 class="text-center">Administracion de Items</h1>
                <div class="container">
                    <div class="table-responsive">
                        <table class="main-table manage-members text-center table table-bordered">
                            <tr>
                                <td>#ID</td>
                                <td>Item</td>
                                <td>Nombre</td>
                                <td>Descripcion</td>
                                <td>Precio</td>
                                <td>Añadir fecha</td>
                                <td>Categoria</td>
                                <td>Nombre</td>
                                <td>Control</td>
                            </tr>
                            
                            <?php
                                foreach($items as $item){
                                    echo "<tr>";
                                        echo "<td>" . $item['Item_ID'] . "</td>";
                                        echo "<td>";
                                            if(empty($item['Image'])){
                                                echo 'Sin imagen';
                                            }else{
                                                echo "<img src='uploads/items/" . $item['Image'] . "' at='' />";
                                            }
                                        echo "</td>";
                                        echo "<td>" . $item['Name'] . "</td>";
                                        echo "<td>" . $item['Description'] . "</td>";
                                        echo "<td>" . $item['Price'] . "</td>";
                                        echo "<td>" . $item['Add_Date'] . "</td>";
                                        echo "<td>" . $item['categoria_name'] . "</td>";
                                        echo "<td>" . $item['Username'] . "</td>";
                                        echo "<td>
                                                <a href='items.php?do=Edit&itemid=" . $item['Item_ID'] . "'  class='btn btn-success'><i class='fa fa-edit'></i> Editar </a>
                                                <a href='items.php?do=Delete&itemid=" . $item['Item_ID'] . "'' class='btn btn-danger confirm'><i class='fa fa-close'></i> Eliminar </a>";
                                                if($item['Approve'] == 0){
                                                    echo "<a 
                                                        href='items.php?do=Approve&itemid=" . $item['Item_ID'] . "'' 
                                                        class='btn btn-info activate'>
                                                        <i class='fa fa-check'></i> Aprovar</a>";
                                                }
                                        echo "</td>";
                                    echo "</tr>";
                                }
                            ?>
                        </table>
                    </div>
                    <a href="items.php?do=add" class="btn btn-sm btn-primary">
                        <i class="fa fa-plus"> Nuevo Item</i>
                    </a>
                </div>
                <?php }else{
                        echo '<div class="container">';
                            echo '<div class="nice-message"> No hay items que mostrar </div>';
                            echo  '<a href="items.php?do=add" class="btn btn-primary">
                                      <i class="fa fa-plus"> Nuevo Item</i>
                                    </a>';
                        echo '</div>';
                    }?>
            <?php
            
        }elseif($do == 'add'){
            //Agregar Items
            ?>    
                <h1 class="text-center">Nuevo Item</h1>

                <div class="container">
                    <form class="form-horizontal" action="?do=insert" method="POST" enctype="multipart/form-data">
                        <!-- Star Nombre Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Item</label> 
                            <div class="col-sm-10 col-md-6">
                                    <input 
                                        type="text" 
                                        name="name" 
                                        class="form-control" 
                                        placeholder="Nombre del Item" required="required" />
                            </div>
                        </div>
                        <!-- End Nombre Field -->
                        <!-- Star Descripcion Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Descripcion</label> 
                            <div class="col-sm-10 col-md-6">
                                    <input 
                                        type="text" 
                                        name="description" 
                                        class="form-control" 
                                        placeholder="Descripcion del item" required="required" />
                            </div>
                        </div>
                        <!-- End Descripcion Field -->
                        <!-- Star Precio Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Precio</label> 
                            <div class="col-sm-10 col-md-6">
                                    <input 
                                        type="text" 
                                        name="price" 
                                        class="form-control" 
                                        placeholder="Precio del item" required="required" />
                            </div>
                        </div>
                        <!-- End Precio Field -->
                        <!-- Star Pais Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Hecho en</label> 
                            <div class="col-sm-10 col-md-6">
                                    <input 
                                        type="text" 
                                        name="country" 
                                        class="form-control" 
                                        placeholder="Pais donde se fabrico" required="required" />
                            </div>
                        </div>
                        <!-- End Pais Field -->
                        <!-- Star Estado Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Estado</label> 
                            <div class="col-sm-10 col-md-6">
                                    <select name="status">
                                        <option value="0">...</option>
                                        <option value="1">Nuevo</option>
                                        <option value="2">Como nuevo</option>
                                        <option value="3">Usado</option>
                                        <option value="4">Muy Viejo</option>
                                    </select>
                            </div>
                        </div>
                        <!-- End Estado Field -->
                        <!-- Star Miembros Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Miembros</label> 
                            <div class="col-sm-10 col-md-6">
                                    <select name="member">
                                        <option value="0">...</option>
                                        <?php
                                            $allMembers = getAllFrom("*", "users", "UserID", "DESC", "", "");
                                            foreach($allMembers as $user){
                                                echo "<option value='" . $user['UserID'] ."'>" . $user['Username'] ."</option>";
                                            }
                                        ?>
                                    </select>
                            </div>
                        </div>
                        <!-- End Miembros Field -->
                         <!-- Star Categorias Field -->
                         <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Categoria</label> 
                            <div class="col-sm-10 col-md-6">
                                    <select name="category">
                                        <option value="0">...</option>
                                        <?php
                                            $allCats = getAllFrom("*", "categories", "ID", "DESC", "where parent = 0", "");
                                            foreach($allCats as $cat){
                                                echo "<option value='" . $cat['ID'] ."'>" . $cat['Name'] ."</option>";
                                                $childCats = getAllFrom("*", "categories", "ID", "DESC", "where parent = {$cat['ID']}", "");
                                                foreach($childCats as $child){
                                                    echo "<option value='" . $child['ID'] ."'>--- " . $child['Name'] ."</option>";
                                                }
                                            }
                                        ?>
                                    </select>
                            </div>
                        </div>
                        <!-- End Categorias Field -->
                        <!-- Star Tags Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Tags</label> 
                            <div class="col-sm-10 col-md-6">
                                    <input 
                                        type="text" 
                                        name="tags" 
                                        class="form-control" 
                                        placeholder="Separa tus Tags con una coma (,)"/>
                            </div>
                        </div>
                        <!-- End Tags Field -->
                        <!-- Star items Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Imagen de Item</label> 
                            <div class="col-sm-10 col-md-6">
                                    <input type="file"  name="image" class="form-control" required="required"/>
                            </div>
                        </div>
                        <!-- End items Field -->
                        <!-- Star Submit Field -->
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                    <input type="submit"  value="Agregar Item" class="btn btn-primary btn-sm"/>
                            </div>
                        </div>
                        <!-- End Submit Field -->
                    </form>
                </div>
            <?php
        }elseif($do == 'insert'){

            if($_SERVER['REQUEST_METHOD'] == 'POST'){

                echo "<h1 class='text-center'>Agregar Item</h1>";
                echo "<div class='container'>";

                //Upload Variables
                $itemName = $_FILES['image']['name'];
                $itemSize = $_FILES['image']['size'];
                $itemTmp = $_FILES['image']['tmp_name'];
                $itemType =  $_FILES['image']['type'];

                //Lista de archivos permitidos para cargar 
                $itemAllowedExtension = array("jpeg", "jpg", "png", "gif");

                //Get item Extension
                $itemNameParts = explode('.', $itemName);
                $itemExtension = strtolower(end($itemNameParts));

                //Get variables del Form
                $name = $_POST['name'];
                $desc = $_POST['description'];
                $price = $_POST['price'];
                $country = $_POST['country'];
                $status = $_POST['status'];
                $cat = $_POST['category'];
                $member = $_POST['member'];
                $tags = $_POST['tags'];
                
                //Validacion del formulario
                $formErrors = array();
                if(empty($name)){
                    $formErrors[] = 'El nombre no puede estar <strong> Vacio</strong>';
                }
                if(empty($desc)){
                    $formErrors[] = 'La descripcion no puede estar <strong> Vacio</strong>';
                }
                if(empty($price)){
                    $formErrors[] = 'El precio no puede estar <strong> vacio </strong>';
                }
                if(empty($country)){
                    $formErrors[] = 'El pais no puede estar <strong> vacio </strong>';
                }
                if($status == 0){
                    $formErrors[] = 'Debes elegir el<strong> estado </strong>';
                }
                if($status == 0){
                    $formErrors[] = 'Debes elegir el<strong> Miembro </strong>';
                }
                if($status == 0){
                    $formErrors[] = 'Debes elegir la<strong> Categoria </strong>';
                }
                if(! empty($itemName) && ! in_array($itemExtension, $itemAllowedExtension)){
                    $formErrors[] = 'Esta extencion no esta <strong> permitida </strong>';
                }
                if(empty($itemName)){
                    $formErrors[] = 'Imagen de item es <strong> requerido </strong>';
                }
                if($itemSize > 8191304){
                    $formErrors[] = 'Imagen de item no puede ser mas grande que <strong> 8MB </strong>';
                }
                foreach($formErrors as $error){
                    echo '<div class="alert alert-danger">' . $error . '</div>';
                }

                //Checa si no hay error para procedere con la opereacion Update
                if(empty($formErrors)){
                    $item_img = rand(0, 1000000000) . '_' . $itemName;
                    move_uploaded_file($itemTmp, "uploads\items\\" . $item_img);

                    //Insertar informacion de usuario en Database
                    $stmt = $con->prepare("INSERT INTO
                                            items(Name, Description, Price, Country_Made, Status, Add_Date, Cat_ID, Member_ID, tags, Image)
                                            VALUES(:zname, :zdesc, :zprice, :zcountry, :zstatus, now(), :zcat, :zmember, :ztags, :zimage)");
                    $stmt->execute(array(
                        'zname' => $name,
                        'zdesc' => $desc,
                        'zprice' => $price,
                        'zcountry' => $country,
                        'zstatus' => $status,
                        'zcat' => $cat,
                        'zmember' => $member,
                        'ztags' => $tags,
                        ':zimage' => $item_img
                    ));
                    //Echo Message Exitoso
                    echo "<div class='container'>";
                        $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Registro Insertado </div>';
                        redirectHome($theMsg, 'back');
                    echo "</div>";
                }
            }else{
                echo "<div class='container'>";
                    $theMsg ='<div class="alert alert-danger"> Lo sentimos, no puedes navegar por esta pagina directamente</div>';
                    redirectHome($theMsg, 'back');
                echo "</div>";
            }
            echo "</div>";
        }elseif($do == 'Edit'){
            //Comprueba si el nombre del Request es numerico y obtinen su valor 
            $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
            //Selecciona todos los datos dependiendo de la ID
            $stmt = $con->prepare("SELECT * FROM  items  WHERE Item_ID = ? ");
            //Executa la Query
            $stmt->execute(array($itemid));
            //Busca el dato
            $item = $stmt->fetch();
            //El recorrido de filas
            $count = $stmt->rowCount();
            //Si el ID existe muestra los datos en el form
            if($stmt->rowCount() > 0){
                ?>    
                    <h1 class="text-center">Editar Item</h1>
                    <div class="container">
                        <form class="form-horizontal" action="?do=Update" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="itemid" value="<?php echo $itemid ?>" />
                            <!-- Star Nombre Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Item</label> 
                                <div class="col-sm-10 col-md-6">
                                        <input 
                                            type="text" 
                                            name="name" 
                                            class="form-control" 
                                            placeholder="Nombre del Item"
                                            value="<?php echo $item['Name'] ?>"/>
                                </div>
                            </div>
                            <!-- End Nombre Field -->
                            <!-- Star Descripcion Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Descripcion</label> 
                                <div class="col-sm-10 col-md-6">
                                        <input 
                                            type="text" 
                                            name="description" 
                                            class="form-control" 
                                            placeholder="Descripcion del item"
                                            value="<?php echo $item['Description'] ?>"/>
                                </div>
                            </div>
                            <!-- End Descripcion Field -->
                            <!-- Star Precio Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Precio</label> 
                                <div class="col-sm-10 col-md-6">
                                        <input 
                                            type="text" 
                                            name="price" 
                                            class="form-control" 
                                            placeholder="Precio del item"
                                            value="<?php echo $item['Price'] ?>"/>
                                </div>
                            </div>
                            <!-- End Precio Field -->
                            <!-- Star Pais Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Hecho en</label> 
                                <div class="col-sm-10 col-md-6">
                                        <input 
                                            type="text" 
                                            name="country" 
                                            class="form-control" 
                                            placeholder="Pais donde se fabrico"
                                            value="<?php echo $item['Country_Made'] ?>"/>
                                </div>
                            </div>
                            <!-- End Pais Field -->
                            <!-- Star Estado Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Estado</label> 
                                <div class="col-sm-10 col-md-6">
                                        <select name="status">
                                            <option value="1" <?php if($item['Status'] == 1){ echo 'selected';} ?>>Nuevo</option>
                                            <option value="2" <?php if($item['Status'] == 2){ echo 'selected';} ?>>Como nuevo</option>
                                            <option value="3" <?php if($item['Status'] == 3){ echo 'selected';} ?>>Usado</option>
                                            <option value="4" <?php if($item['Status'] == 4){ echo 'selected';} ?>>Muy Viejo</option>
                                        </select>
                                </div>
                            </div>
                            <!-- End Estado Field -->
                            <!-- Star Miembros Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Miembros</label> 
                                <div class="col-sm-10 col-md-6">
                                        <select name="member">
                                            <?php
                                                $stmt = $con->prepare("SELECT * FROM users");
                                                $stmt->execute();
                                                $users = $stmt->fetchAll();
                                                foreach($users as $user){
                                                    echo "<option value='" . $user['UserID'] ."'"; 
                                                    if($item['Member_ID'] == $user['UserID']){ echo 'selected';} 
                                                    echo ">" . $user['Username'] ."</option>";
                                                }
                                            ?>
                                        </select>
                                </div>
                            </div>
                            <!-- End Miembros Field -->
                            <!-- Star Categorias Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Categoria</label> 
                                <div class="col-sm-10 col-md-6">
                                        <select name="category">
                                            <?php
                                                $stmt2 = $con->prepare("SELECT * FROM categories");
                                                $stmt2->execute();
                                                $cats = $stmt2->fetchAll();
                                                foreach($cats as $cat){
                                                    echo "<option value='" . $cat['ID'] ."'";
                                                    if($item['Cat_ID'] == $cat['ID']){ echo 'selected';} 
                                                    echo ">" . $cat['Name'] ."</option>";
                                                }
                                            ?>
                                        </select>
                                </div>
                            </div>
                            <!-- End Categorias Field -->
                            <!-- Star Tags Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Tags</label> 
                                <div class="col-sm-10 col-md-6">
                                        <input 
                                            type="text" 
                                            name="tags" 
                                            class="form-control" 
                                            placeholder="Separa tus Tags con una coma (,)"
                                            value="<?php echo $item['tags'] ?>"/>
                                </div>
                            </div>
                            <!-- End Tags Field -->
                            <!-- Star items Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Imagen de Item</label> 
                                <div class="col-sm-10 col-md-6">
                                        <input 
                                            type="file"  
                                            name="image" 
                                            class="form-control" 
                                            required="required"/>
                                </div>
                            </div>
                            <!-- End items Field -->
                            <!-- Star Submit Field -->
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                        <input type="submit"  value="Actualizar Item" class="btn btn-primary btn-sm"/>
                                </div>
                            </div>
                            <!-- End Submit Field -->
                        </form>
                        <?php
                        //Selecciona todos los usaurios excepto el ADM
                        $stmt = $con->prepare("SELECT
                                                    comments.*, users.Username AS Member   
                                                FROM
                                                    comments
                                                INNER JOIN
                                                    users
                                                ON
                                                    users.UserID = comments.user_id
                                                WHERE item_id = ?");
                        //Executa accion
                        $stmt->execute(array($itemid));
                        //Asigna las variables
                        $row = $stmt->fetchAll();

                        if(! empty($row)){
                        ?>
                        <h1 class="text-center">Seguimiento de [<?php echo $item['Name'] ?>] Comentarios</h1>
                        <div class="table-responsive">
                            <table class="main-table text-center table table-bordered">
                                <tr>
                                    <td>Comentario</td>
                                    <td>Nombre de usuario</td>
                                    <td>Dato de registro</td>
                                    <td>Control</td>
                                </tr>
                                <?php
                                    foreach($row as $row){
                                        echo "<tr>";
                                            echo "<td>" . $row['comment'] . "</td>";
                                            echo "<td>" . $row['Member'] . "</td>";
                                            echo "<td>" . $row['comment_date'] . "</td>";
                                            echo "<td>
                                                    <a href='comments.php?do=Edit&comid=" . $row['c_id'] . "'  
                                                        class='btn btn-success'><i class='fa fa-edit'></i> Editar </a>
                                                    <a href='comments.php?do=Delete&comid=" . $row['c_id'] . "''
                                                        class='btn btn-danger confirm'><i class='fa fa-close'></i> Eliminar </a>";
                                                    if($row['status'] == 0){
                                                        echo "<a href='comments.php?do=Approve&comid=" . $row['c_id'] . "'' 
                                                        class='btn btn-info activate'>
                                                        <i class='fa fa-check'></i> Aprovar</a>";
                                                    }
                                            echo "</td>";
                                        echo "</tr>";
                                    }
                                ?>
                                <tr>
                            </table>
                        </div>
                        <?php }?>
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
            echo "<h1 class='text-center'>Actualizar Item</h1>";
                echo "<div class='container'>";

                if($_SERVER['REQUEST_METHOD'] == 'POST'){

                    //Upload Variables
                    $itemName = $_FILES['image']['name'];
                    $itemSize = $_FILES['image']['size'];
                    $itemTmp = $_FILES['image']['tmp_name'];
                    $itemType =  $_FILES['image']['type'];

                    //Lista de archivos permitidos para cargar 
                    $itemAllowedExtension = array("jpeg", "jpg", "png", "gif");

                    //Get item Extension
                    $itemNameParts = explode('.', $itemName);
                    $itemExtension = strtolower(end($itemNameParts));

                    //Get variables del Form
                    $id = $_POST['itemid'];
                    $name = $_POST['name'];
                    $desc = $_POST['description'];
                    $price = $_POST['price'];
                    $country = $_POST['country'];
                    $status = $_POST['status'];
                    $cat = $_POST['category'];
                    $member = $_POST['member'];
                    $tags = $_POST['tags'];

                     //Validacion del formulario
                    $formErrors = array();
                    if(empty($name)){
                        $formErrors[] = 'El nombre no puede estar <strong> Vacio</strong>';
                    }
                    if(empty($desc)){
                        $formErrors[] = 'La descripcion no puede estar <strong> Vacio</strong>';
                    }
                    if(empty($price)){
                        $formErrors[] = 'El precio no puede estar <strong> vacio </strong>';
                    }
                    if(empty($country)){
                        $formErrors[] = 'El pais no puede estar <strong> vacio </strong>';
                    }
                    if($status == 0){
                        $formErrors[] = 'Debes elegir el<strong> estado </strong>';
                    }
                    if($status == 0){
                        $formErrors[] = 'Debes elegir el<strong> Miembro </strong>';
                    }
                    if($status == 0){
                        $formErrors[] = 'Debes elegir la<strong> Categoria </strong>';
                    }
                    if(! empty($itemName) && ! in_array($itemExtension, $itemAllowedExtension)){
                        $formErrors[] = 'Esta extencion no esta <strong> permitida </strong>';
                    }
                    if(empty($itemName)){
                        $formErrors[] = 'Imagen de item es <strong> requerido </strong>';
                    }
                    if($itemSize > 8191304){
                        $formErrors[] = 'Imagen de item no puede ser mas grande que <strong> 8MB </strong>';
                    }
                    foreach($formErrors as $error){
                        echo '<div class="alert alert-danger">' . $error . '</div>';
                    }

                    if(empty($formErrors)){
                        $item_img = rand(0, 1000000000) . '_' . $itemName;
                        move_uploaded_file($itemTmp, "uploads\items\\" . $item_img);

                        //Update la Database con esta info
                        $stmt = $con->prepare("UPDATE 
                                                    items 
                                                SET 
                                                    Name = ?,
                                                    Description = ?,
                                                    Price = ?,
                                                    Country_Made = ?,
                                                    Status = ?,
                                                    Cat_ID = ?,
                                                    Member_ID = ?,
                                                    tags = ?,
                                                    Image = ?
                                                WHERE  
                                                    Item_ID = ?");
                        $stmt->execute(array($name, $desc, $price, $country, $status, $cat, $member, $tags, $item_img, $id));

                        //Echo Message Exitoso
                        $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Registro Actualizado </div>';
                    
                        redirectHome($theMsg, 'back');
                        
                    }

                }else{

                    $theMsg = '<div class="alert alert-danger">Lo sentimos, no puedes navegar por esta pagina directamente </div>';
                    redirectHome($theMsg, 'back');
                }
                echo "</div>";
        }elseif($do == 'Delete'){
            //Page Eliminar miembro
            echo "<h1 class='text-center'>Item Eliminado</h1>";
            echo "<div class='container'>";

            //Comprueba si el itemid del Request es numerico y obtinen su valor 
            $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;

            //Selecciona todos los datos dependiendo de la ID
            $check = checkItem('Item_ID', 'items', $itemid);
            
            //Si el ID existe muestra los datos en el form
            if($check > 0){

                $stmt = $con->prepare("DELETE FROM items WHERE Item_ID = :zid");
                $stmt->bindParam(":zid", $itemid);
                $stmt->execute();

                $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Registro Eliminado </div>';
                redirectHome($theMsg, 'back');

            }else{
                $theMsg = '<div class="alert alert-danger">Esta ID no existe </div>';
                redirectHome($theMsg, 'back');
            }
            echo "</div>";

        }elseif($do == 'Approve'){
            //Page Eliminar miembro
            echo "<h1 class='text-center'>Item Activado</h1>";
            echo "<div class='container'>";

            //Comprueba si el ID del Request es numerico y obtinen su valor 
            $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;

            //Selecciona todos los datos dependiendo de la ID
            $check = checkItem('Item_ID', 'items', $itemid);
            
            //Si el ID existe muestra los datos en el form
            if($check > 0){

                $stmt = $con->prepare("UPDATE items SET Approve = 1 WHERE Item_ID = ?");
                $stmt->execute(array($itemid));

                $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Registro Activado </div>';
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
