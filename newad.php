<?php
    session_start();
    $pageTitle = 'Crea Nuevo Item';
    include 'init.php';

    if(isset($_SESSION['user'])){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            
            $formErrors = array();

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

            $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $desc = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
            $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_INT);
            $country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);
            $status = filter_var($_POST['status'], FILTER_SANITIZE_NUMBER_INT);
            $category = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
            $tags = filter_var($_POST['tags'], FILTER_SANITIZE_STRING);

            
            

            if(strlen($name)<4){
                $formErrors[] = 'El titulo del articulo debe tener al menos 4 caracteres';
            }
            if(strlen($desc)<10){
                $formErrors[] = 'La descripcion del elemento debe tener alemnos 10 caracteres';
            }
            if(strlen($country)<2){
                $formErrors[] = 'Hecho en: debe tener almenos 2 caracteres';
            }
            if(empty($price)){
                $formErrors[] = 'El precio no debe estar vacio';
            }
            if(empty($status)){
                $formErrors[] = 'El estado no debe estar vacio';
            }
            if(empty($category)){
                $formErrors[] = 'La categoria no debe estar vacia';
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
            if(empty($formErrors)){
                $item_img = rand(0, 1000000000) . '_' . $itemName;
                move_uploaded_file($itemTmp, "admin\uploads\items\\" . $item_img);  

                //Insertar informacion de usuario en Database
                $stmt = $con->prepare("INSERT INTO
                                        items(Name, Description, Price, Country_Made, Image, Status, Add_Date, Cat_ID, Member_ID, tags)
                                        VALUES(:zname, :zdesc, :zprice, :zcountry, :zimage, :zstatus, now(), :zcat, :zmember, :ztags)");
                $stmt->execute(array(
                    'zname' => $name,
                    'zdesc' => $desc,
                    'zprice' => $price,
                    'zcountry' => $country,
                    ':zimage' => $item_img,
                    'zstatus' => $status,
                    'zcat' => $category,
                    'zmember' => $_SESSION['uid'],
                    'ztags' => $tags
                ));
                //Echo Message Exitoso
                if($stmt){
                    $succesMsg = 'El Item ha sido agregado';
                }
            }
        }
    ?>
        <h1 class="text-center"><?php echo $pageTitle ?></h1>
        <div class="create-ad block">
            <div class="container">
                <div class="panel panel-primary">
                    <div class="panel-heading"><?php echo $pageTitle ?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <form class="form-horizontal main-form" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
                                    <!-- Star Nombre Field -->
                                    <div class="form-group form-group-lg">
                                        <label class="col-sm-3 control-label">Nombre</label> 
                                        <div class="col-sm-10 col-md-9">
                                                <input 
                                                    pattern=".{4,}"
                                                    title="Este campo requiere al menos 4 caracteres"
                                                    type="text" 
                                                    name="name" 
                                                    class="form-control live" 
                                                    placeholder="Nombre del Item"
                                                    data-class=".live-title"
                                                    required/>
                                        </div>
                                    </div>
                                    <!-- End Nombre Field -->
                                    <!-- Star Descripcion Field -->
                                    <div class="form-group form-group-lg">
                                        <label class="col-sm-3 control-label">Descripcion</label> 
                                        <div class="col-sm-10 col-md-9">
                                                <input 
                                                    pattern=".{10,}"
                                                    title="Este campo requiere al menos 10 caracteres"
                                                    type="text" 
                                                    name="description" 
                                                    class="form-control live" 
                                                    placeholder="Descripcion del item"
                                                    required
                                                    data-class=".live-desc"/>
                                        </div>
                                    </div>
                                    <!-- End Descripcion Field -->
                                    <!-- Star Precio Field -->
                                    <div class="form-group form-group-lg">
                                        <label class="col-sm-3 control-label">Precio</label> 
                                        <div class="col-sm-10 col-md-9">
                                                <input 
                                                    type="text" 
                                                    name="price" 
                                                    class="form-control live" 
                                                    placeholder="Precio del item"
                                                    required
                                                    data-class=".live-price"/>
                                        </div>
                                    </div>
                                    <!-- End Precio Field -->
                                    <!-- Star Pais Field -->
                                    <div class="form-group form-group-lg">
                                        <label class="col-sm-3 control-label">Hecho en</label> 
                                        <div class="col-sm-10 col-md-9">
                                                <input 
                                                    type="text" 
                                                    name="country" 
                                                    class="form-control" 
                                                    placeholder="Pais donde se fabrico"
                                                    required
                                                     />
                                        </div>
                                    </div>
                                    <!-- End Pais Field -->
                                    <!-- Star items Field -->
                                    <div class="form-group form-group-lg">
                                        <label class="col-sm-3 control-label">Imagen de Item</label> 
                                        <div class="col-sm-10 col-md-9">
                                                <input 
                                                    type="file"  
                                                    name="image" 
                                                    class="form-control" 
                                                    required="required"/>
                                        </div>
                                    </div>
                                    <!-- End items Field -->  
                                    <!-- Star Estado Field -->
                                    <div class="form-group form-group-lg">
                                        <label class="col-sm-3 control-label">Estado</label> 
                                        <div class="col-sm-10 col-md-9">
                                                <select name="status" required>
                                                    <option value="0">...</option>
                                                    <option value="1">Nuevo</option>
                                                    <option value="2">Como nuevo</option>
                                                    <option value="3">Usado</option>
                                                    <option value="4">Muy Viejo</option>
                                                </select>
                                        </div>
                                    </div>
                                    <!-- End Estado Field -->
                                    <!-- Star Categorias Field -->
                                    <div class="form-group form-group-lg">
                                        <label class="col-sm-3 control-label">Categoria</label> 
                                        <div class="col-sm-10 col-md-9">
                                                <select name="category" required>
                                                    <option value="0">...</option>
                                                    <?php
                                                        $cats = getAllFrom('*', 'categories', 'ID', '', '');
                                                        foreach($cats as $cat){
                                                            echo "<option value='" . $cat['ID'] ."'>" . $cat['Name'] ."</option>";
                                                        }
                                                    ?>
                                                </select>
                                        </div>
                                    </div>
                                    <!-- End Categorias Field -->
                                    <!-- Star Tags Field -->
                                    <div class="form-group form-group-lg">
                                        <label class="col-sm-3 control-label">Tags</label> 
                                        <div class="col-sm-10 col-md-9">
                                                <input 
                                                    type="text" 
                                                    name="tags" 
                                                    class="form-control" 
                                                    placeholder="Separa tus Tags con una coma (,)"/>
                                        </div>
                                    </div>
                                    <!-- End Tags Field -->
                                    <!-- Star Submit Field -->
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-10">
                                                <input type="submit"  value="Agregar Item" class="btn btn-primary btn-sm"/>
                                        </div>
                                    </div>
                                    <!-- End Submit Field -->
                                </form>
                            </div>
                            <div class="col-md-4">
                                <div class="thumbnail item-box live-preview">
                                    <span class="price-tag">
                                        $<span class="live-price">0</span>
                                    </span>
                                    <img class="live-img img-responsive" src="R.png" alt="" />
                                    <div class="caption">
                                        <h3 class="live-title">Titulo</h3>
                                        <p class="live-desc">Descripcion</p>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <!-- Star bucle atraves de errores -->
                        <?php 
                            if(! empty($formErrors)){
                                foreach($formErrors as $error){
                                    echo '<div class="alert alert-danger">' . $error . '</div>';
                                }
                            }
                            if(isset($succesMsg)){
                                echo '<div class="alert alert-success">' . $succesMsg . '</div>';
                            }
                        ?>
                        <!-- End bucle atraves de errores -->
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
?>