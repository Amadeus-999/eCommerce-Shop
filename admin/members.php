<?php
    /*
    **Administrador de miembros
    **Se podra Add| Edit | Delete Miembros desde aqui|
    */
    ob_start(); //inicio del output buffering 
    session_start();

    $pageTitle = 'Miembros';
    
    if(isset($_SESSION['Username'])){
        include 'init.php';


        $do = isset($_GET['do']) ? $_GET['do']: 'Manage';

        //Inicio de Adm Page
        if($do == 'Manage'){//Administarcion Miembros Page
            
            $query = '';
            if(isset($_GET['page']) && $_GET['page'] == 'Venta'){
                $query = 'AND RegStatus = 0';
            }

            //Selecciona todos los usaurios excepto el ADM
            $stmt = $con->prepare("SELECT * FROM users WHERE GroupID != 1 $query ORDER BY UserID DESC");
            //Executa accion
            $stmt->execute();
            //Asigna las variables
            $rows = $stmt->fetchAll();

            if(! empty($rows)){
            ?>
                <h1 class="text-center">Administracion de Miembros</h1>
                <div class="container">
                    <div class="table-responsive">
                        <table class="main-table manage-members text-center table table-bordered">
                            <tr>
                                <td>#ID</td>
                                <td>Avatar</td>
                                <td>Nombre de Usuario</td>
                                <td>Email</td>
                                <td>Nombre Completo</td>
                                <td>Dato de registro</td>
                                <td>Control</td>
                            </tr>
                            
                            <?php
                                foreach($rows as $row){
                                    echo "<tr>";
                                        echo "<td>" . $row['UserID'] . "</td>";
                                        echo "<td>";
                                            if(empty($row['avatar'])){
                                                echo 'Sin imagen';
                                            }else{
                                                echo "<img src='uploads/avatars/" . $row['avatar'] . "' at='' />";
                                            }
                                        echo "</td>";
                                        echo "<td>" . $row['Username'] . "</td>";
                                        echo "<td>" . $row['Email'] . "</td>";
                                        echo "<td>" . $row['FullName'] . "</td>";
                                        echo "<td>" . $row['Date'] . "</td>";
                                        echo "<td>
                                                <a href='members.php?do=Edit&userid=" . $row['UserID'] . "'  class='btn btn-success'><i class='fa fa-edit'></i> Editar </a>
                                                <a href='members.php?do=Delete&userid=" . $row['UserID'] . "'' class='btn btn-danger confirm'><i class='fa fa-close'></i> Eliminar </a>";
                                                if($row['RegStatus'] == 0){
                                                    echo "<a href='members.php?do=Activate&userid=" . $row['UserID'] . "'' 
                                                    class='btn btn-info activate'>
                                                    <i class='fa fa-check'></i> Activar</a>";
                                                }
                                        echo "</td>";
                                    echo "</tr>";
                                }
                            ?>
                        </table>
                    </div>
                    <a href="members.php?do=add" class="btn btn-primary">
                        <i class="fa fa-plus"> Nuevo miembro</i>
                    </a>
                </div>
                <?php }else{
                        echo '<div class="container">';
                            echo '<div class="nice-message"> No hay miembros que mostrar </div>';
                            echo '<a href="members.php?do=add" class="btn btn-primary">
                                        <i class="fa fa-plus"> Nuevo miembro</i>
                                    </a>';
                        echo '</div>';
                    }?>
            <?php
        }elseif($do == 'add'){
            //Agregar miembros
            ?>    
                <h1 class="text-center">Miembro Nuevo</h1>

                <div class="container">
                    <form class="form-horizontal" action="?do=insert" method="POST" enctype="multipart/form-data">
                        <!-- Star username Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Nombre de Usuario</label> 
                            <div class="col-sm-10 col-md-6">
                                    <input type="text" name="username" class="form-control" autocomplete="off" required="required" placeholder="Nombre de usuarios para inicar sesion el la tienda"/>
                            </div>
                        </div>
                        <!-- End username Field -->
                        <!-- Star Password Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Password</label> 
                            <div class="col-sm-10 col-md-6">
                                <input type="Password"  name="password" class="password form-control" required="required" autocomplete="new-password" placeholder="La contraseña debe ser dificil y compleja"/>
                                <i class="show-pass fa fa-eye fa-2x"></i>
                            </div>
                        </div>
                        <!-- End Password Field -->
                        <!-- Star Email Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Email</label> 
                            <div class="col-sm-10 col-md-6">
                                    <input type="Email"  name="email" class="form-control" required="required" placeholder="El email debe ser valido"/>
                            </div>
                        </div>
                        <!-- End Email Field -->
                        <!-- Star FullName Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Nombre Completo</label> 
                            <div class="col-sm-10 col-md-6">
                                    <input type="text"  name="full" class="form-control" required="required" placeholder="El nombre completo aparece en su pagina de perfil"/>
                            </div>
                        </div>
                        <!-- End FullName Field -->
                        <!-- Star Avatar Field -->
                        <div class="form-group form-group-lg">
                            <label class="col-sm-2 control-label">Avatar de Usuario</label> 
                            <div class="col-sm-10 col-md-6">
                                    <input type="file"  name="avatar" class="form-control" required="required"/>
                            </div>
                        </div>
                        <!-- End Avatar Field -->
                        <!-- Star Submit Field -->
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                    <input type="submit"  value="Agregar Miembro" class="btn btn-primary btn-lg"/>
                            </div>
                        </div>
                        <!-- End Submit Field -->
                    </form>
                </div>
            <?php
        }elseif($do == 'insert'){

            //Pagina para insertar miembros

            if($_SERVER['REQUEST_METHOD'] == 'POST'){

                echo "<h1 class='text-center'>Agregar Miembro</h1>";
                echo "<div class='container'>";

                //Upload Variables
                $avatarName = $_FILES['avatar']['name'];
                $avatarSize = $_FILES['avatar']['size'];
                $avatarTmp = $_FILES['avatar']['tmp_name'];
                $avatarType =  $_FILES['avatar']['type'];

                //Lista de archivos permitidos para cargar 
                $avatarAllowedExtension = array("jpeg", "jpg", "png", "gif");

                //Get Avatar Extension
                $avatarNameParts = explode('.', $avatarName);
                $avatarExtension = strtolower(end($avatarNameParts));

                //Get variables del Form
                $user = $_POST['username'];
                $pass = $_POST['password'];
                $email = $_POST['email'];
                $name = $_POST['full'];
                
                $hashPass = sha1($_POST['password']);
                //Validacion del formulario
                $formErrors = array();
                if(strlen($user)<4){
                    $formErrors[] = 'Nombre de Usuario no puede tener menos de <strong> 4 caracteres </strong>';
                }
                if(strlen($user)>20){
                    $formErrors[] = 'Nombre Completo no puede tener mas de <strong> 20 caracteres </strong>';
                }
                if(empty($user)){
                    $formErrors[] = 'Nombre de usuario no puede estar <strong> vacio </strong>';
                }
                if(empty($pass)){
                    $formErrors[] = 'Password no puede estar <strong> vacio </strong>';
                }
                if(empty($name)){
                    $formErrors[] = 'Nombre Completo no puede estar <strong> vacio </strong>';
                }
                if(empty($email)){
                    $formErrors[] = 'Email no puede estar <strong> vacio </strong>';
                }
                if(! empty($avatarName) && ! in_array($avatarExtension, $avatarAllowedExtension)){
                    $formErrors[] = 'Esta extencion no esta <strong> permitida </strong>';
                }
                if(empty($avatarName)){
                    $formErrors[] = 'Imagen de Avatar es <strong> requerido </strong>';
                }
                if($avatarSize > 8191304){
                    $formErrors[] = 'Imagen de Avatar no puede ser mas grande que <strong> 8MB </strong>';
                }
                foreach($formErrors as $error){
                    echo '<div class="alert alert-danger">' . $error . '</div>';
                }

                //Checa si no hay error para procedere con la opereacion Update
                if(empty($formErrors)){
                    //image file
                    $avatar = rand(0, 1000000000) . '_' . $avatarName;
                    move_uploaded_file($avatarTmp, "uploads\avatars\\" . $avatar);
                    
                    //Checa si el usuario existe en la Database
                    $check = checkItem("Username", "users", $user);

                    if($check == 1){
                        $theMsg = "<div class='alert alert-danger'>" . ' Perdon, este usuario ya existe</div>';
                        redirectHome($theMsg, 'back');
                    } else{
                        //Insertar informacion de usuario en Database
                        $stmt = $con->prepare("INSERT INTO
                                                users(Username, Password, Email, FullName, RegStatus, Date, avatar)
                                                VALUES(:zuser, :zpass, :zemail, :zname, 1, now(), :zavatar) ");
                        $stmt->execute(array(
                            'zuser' => $user,
                            'zpass' => $hashPass,
                            'zemail' => $email,
                            'zname' => $name,
                            'zavatar' => $avatar
                        ));
                        //Echo Message Exitoso
                        echo "<div class='container'>";
                            $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Registro Insertado </div>';
                            redirectHome($theMsg, 'back');
                        echo "</div>";
                    }
                }
            }else{
                echo "<div class='container'>";
                    $theMsg ='<div class="alert alert-danger"> Lo sentimos, no puedes navegar por esta pagina directamente</div>';
                    redirectHome($theMsg, 'back');
                echo "</div>";
            }
            echo "</div>";
            


        }elseif($do == 'Edit'){//Edit Page
            //Comprueba si el ID del Request es numerico y obtinen su valor 
            $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
            //Selecciona todos los datos dependiendo de la ID
            $stmt = $con->prepare("SELECT * FROM  users  WHERE UserID = ? LIMIT 1");
            //Executa la Query
            $stmt->execute(array($userid));
            //Busca el dato
            $row = $stmt->fetch();
            //El recorrido de filas
            $count = $stmt->rowCount();
            //Si el ID existe muestra los datos en el form
            if($stmt->rowCount() > 0){
                ?>    
                    <h1 class="text-center">Editar Miembros</h1>

                    <div class="container">
                        <form class="form-horizontal" action="?do=Update" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="userid" value="<?php echo $userid ?>" />
                            <!-- Star username Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Nombre de Usuario</label> 
                                <div class="col-sm-10 col-md-4">
                                        <input type="text" name="username" class="form-control" value="<?php echo $row['Username'] ?>" autocomplete="off" required="required"/>
                                </div>
                            </div>
                            <!-- End username Field -->
                            <!-- Star Password Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Password</label> 
                                <div class="col-sm-10 col-md-4">
                                    <input type="hidden"  name="oldpassword" value="<?php echo $row['Password'] ?>"/>
                                    <input type="Password"  name="newpassword" class="form-control"  autocomplete="new-password" placeholder="Deja en blanco si no quieres cambiar"/>
                                </div>
                            </div>
                            <!-- End Password Field -->
                            <!-- Star Email Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Email</label> 
                                <div class="col-sm-10 col-md-4">
                                        <input type="Email"  name="email" value="<?php echo $row['Email'] ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                            <!-- End Email Field -->
                            <!-- Star FullName Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Nombre Completo</label> 
                                <div class="col-sm-10 col-md-4">
                                        <input type="text"  name="full" value="<?php echo $row['FullName'] ?>" class="form-control" required="required"/>
                                </div>
                            </div>
                            <!-- End FullName Field -->
                            <!-- Star Avatar Field -->
                            <div class="form-group form-group-lg">
                                <label class="col-sm-2 control-label">Avatar de Usuario</label> 
                                <div class="col-sm-10 col-md-4">
                                        <input type="file"  name="avatar" class="form-control" required="required"/>
                                </div>
                            </div>
                            <!-- End Avatar Field -->
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
                echo "<h1 class='text-center'>Actualizar Miembro</h1>";
                echo "<div class='container'>";

                if($_SERVER['REQUEST_METHOD'] == 'POST'){

                    //Upload Variables
                    $avatarName = $_FILES['avatar']['name'];
                    $avatarSize = $_FILES['avatar']['size'];
                    $avatarTmp = $_FILES['avatar']['tmp_name'];
                    $avatarType =  $_FILES['avatar']['type'];

                    //Lista de archivos permitidos para cargar 
                    $avatarAllowedExtension = array("jpeg", "jpg", "png", "gif");

                    //Get Avatar Extension
                    $avatarNameParts = explode('.', $avatarName);
                    $avatarExtension = strtolower(end($avatarNameParts));
                    //Get variables del Form
                    $id = $_POST['userid'];
                    $user = $_POST['username'];
                    $email = $_POST['email'];
                    $name = $_POST['full'];

                    //Password Trick Condicion ? True : False
                    $pass = empty($_POST['newpassword']) ? $pass = $_POST['oldpassword'] : $pass = sha1($_POST['newpassword']);
                    
                    //Validacion del formulario
                    $formErrors = array();
                    if(strlen($user)<4){
                        $formErrors[] = 'Nombre de Usuario no puede tener menos de <strong> 4 caracteres </strong>';
                    }
                    if(strlen($user)>20){
                        $formErrors[] = 'Nombre Usuario no puede tener mas de <strong> 20 caracteres </strong>';
                    }
                    if(empty($user)){
                        $formErrors[] = 'Nombre de Usuario no puede estar <strong> vacio </strong>';
                    }
                    if(empty($name)){
                        $formErrors[] = 'Nombre Completo no puede estar <strong> vacio </strong>';
                    }
                    if(empty($email)){
                        $formErrors[] = 'Email no puede estar <strong> vacio </strong>';
                    }
                    if(! empty($avatarName) && ! in_array($avatarExtension, $avatarAllowedExtension)){
                        $formErrors[] = 'Esta extencion no esta <strong> permitida </strong>';
                    }
                    if(empty($avatarName)){
                        $formErrors[] = 'Imagen de Avatar es <strong> requerido </strong>';
                    }
                    if($avatarSize > 8191304){
                        $formErrors[] = 'Imagen de Avatar no puede ser mas grande que <strong> 8MB </strong>';
                    }
                    foreach($formErrors as $error){
                        echo '<div class="alert alert-danger">' . $error . '</div>';
                    }

                    //Checa si no hay error para procedere con la opereacion Update
                    if(empty($formErrors)){
                        //image file
                        $avatar = rand(0, 1000000000) . '_' . $avatarName;
                        move_uploaded_file($avatarTmp, "uploads\avatars\\" . $avatar);

                        $stmt2 = $con->prepare("SELECT 
                                                    *
                                                FROM
                                                    users
                                                WHERE
                                                    Username = ?
                                                AND
                                                    UserID != ?");

                        $stmt2->execute(array($user, $id));
                        $count = $stmt2->rowCount();

                        if($count == 1){
                            $theMsg = '<div class="alert alert-danger">Perdon, este usuario ya existe </div>';
                            redirectHome($theMsg, 'back');
                        }else{
                            //Update la Database con esta info
                            $stmt = $con->prepare("UPDATE users SET Username = ?, Email = ?, FullName = ?, Password = ?, avatar = ? WHERE  UserID = ?");
                            $stmt->execute(array($user, $email, $name, $pass, $avatar, $id));

                            //Echo Message Exitoso
                            $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Registro Actualizado </div>';
                        
                            redirectHome($theMsg, 'back');
                        }
                    }

                }else{

                    $theMsg = '<div class="alert alert-danger">Lo sentimos, no puedes navegar por esta pagina directamente </div>';
                    redirectHome($theMsg, 'back');
                }
                echo "</div>";
            }elseif($do == 'Delete'){
                //Page Eliminar miembro
                echo "<h1 class='text-center'>Miembro Eliminado</h1>";
                echo "<div class='container'>";

                //Comprueba si el ID del Request es numerico y obtinen su valor 
                $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;

                //Selecciona todos los datos dependiendo de la ID
                $check = checkItem('userid', 'users', $userid);
                
                //Si el ID existe muestra los datos en el form
                if($check > 0){

                    $stmt = $con->prepare("DELETE FROM users WHERE UserID = :zuser");
                    $stmt->bindParam(":zuser", $userid);
                    $stmt->execute();

                    //mensaje de confirmacion
                    $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Registro Eliminado </div>';
                    redirectHome($theMsg, 'back');

                }else{
                    $theMsg = '<div class="alert alert-danger">Esta ID no existe </div>';
                    redirectHome($theMsg, 'back');
                }
                echo "</div>";
            }elseif($do == 'Activate'){
                //Page Eliminar miembro
                echo "<h1 class='text-center'>Miembro Activado</h1>";
                echo "<div class='container'>";

                //Comprueba si el ID del Request es numerico y obtinen su valor 
                $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;

                //Selecciona todos los datos dependiendo de la ID
                $check = checkItem('userid', 'users', $userid);
                
                //Si el ID existe muestra los datos en el form
                if($check > 0){

                    $stmt = $con->prepare("UPDATE users SET RegStatus = 1 WHERE UserID = ?");
                    $stmt->execute(array($userid));

                    //mensaje de confirmacion
                    $theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Registro Activado </div>';
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