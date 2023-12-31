<?php
    ob_start();
    session_start();
    $pageTitle = 'Login';
    
    if(isset($_SESSION['user'])){
        header('Location: index.php');
    }
    include 'init.php';

    //Comprueba si el User proviene HTTP Post Request
    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        if(isset($_POST['login'])){
            $user = $_POST['username'];
            $pass = $_POST['password'];
            
            $hashedPass = sha1($pass);
            
            //Verfica si se carga
            //echo $user . ' ' . $pass;

            //Comprueba si el User existe en Database
            $stmt = $con->prepare("SELECT 
                                        UserID, Username, Password 
                                    FROM 
                                        users 
                                    WHERE 
                                        Username = ? 
                                    AND 
                                        Password = ? ");

            $stmt->execute(array($user, $hashedPass));
            $get = $stmt->fetch();
            $count = $stmt->rowCount();

            if($count>0){
                $_SESSION['user'] = $user; //Registro de Nombre de Sesion

                $_SESSION['uid'] = $get['UserID'];

                header('Location: index.php'); //Redireccion a Dash
                exit();
            }
        }else{
            $formErrors = array();

            //Upload Variables
            $avatarName = $_FILES['avatar']['name'];
            $avatarSize = $_FILES['avatar']['size'];
            $avatarTmp = $_FILES['avatar']['tmp_name'];
            $avatarType =  $_FILES['avatar']['type'];

            //Lista de archivos permitidos para cargar 
            $avatarAllowedExtension = array("jpeg", "jpg", "png", "gif");

            //Get Avatar Extension
            
            /*ERROR POR 'explode' no de vuelve la variable por referencia que espera 'end' 
            $avatarExtension = strtolower(end(explode('.', $avatarName)));
            */
            $avatarNameParts = explode('.', $avatarName);
            $avatarExtension = strtolower(end($avatarNameParts));

            $username = $_POST['username'];
            $password = $_POST['password'];
            $password2 = $_POST['password2'];
            $email = $_POST['email'];

            

            if(isset($username)){
                $filterdUser = filter_var($_POST['username'], FILTER_SANITIZE_STRING);

                if(strlen($filterdUser)<4){
                    $formErrors[] = 'Username no debe de tener menos de 4 caracteres';
                }
            }
            if(isset($password) && isset($password2)){
                if(empty($password)){
                    $formErrors[] = 'Perdon, la contraseña no puede estar vacia';
                }
                
                $pass1 = sha1($password);
                $pass2 = sha1($password2);

                if($pass1 !== $pass2){
                    $formErrors[] = 'Perdon, las contraseñas no coinciden';
                }
            }
            if(isset($email)){
                $filterdEmail = filter_var($email, FILTER_SANITIZE_EMAIL);

                if(filter_var($filterdEmail, FILTER_VALIDATE_EMAIL) != true){
                    $formErrors[] = 'Este correo no es valido';
                }
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
            //Checa si no hay error para procedere con Agregar el user
            if(empty($formErrors)){
                //image file
                $avatar = rand(0, 1000000000) . '_' . $avatarName;
                move_uploaded_file($avatarTmp, "admin\uploads\avatars\\" . $avatar);

                //Checa si el usuario existe en la Database
                $check = checkItem("Username", "users", $username);

                if($check == 1){
                    $formErrors[] = "Perdon, este usuario ya existe ";
                } else{
                    //Insertar informacion de usuario en Database
                    $stmt = $con->prepare("INSERT INTO
                                            users(Username, Password, Email, RegStatus, Date, avatar)
                                            VALUES(:zuser, :zpass, :zemail, 0, now(), :zavatar) ");
                    $stmt->execute(array(
                        'zuser' => $username,
                        'zpass' => sha1($password),
                        'zemail' => $email,
                        'zavatar' => $avatar
                    ));
                    //Echo Message Exitoso
                    $succesMsg = 'Felicidades, tu usuario ha sido registrado';
                }
            }
        }
    }
?>

<div class="container login-page">
    <h1 class="text-center">
        <span class="selected" data-class="login">Login</span> | 
        <span data-class="signup">Signup</span>
    </h1>
    <!-- Start Login Form -->
    <form class="login" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <div class="input-container">
            <input 
                class="form-control" 
                type="text" 
                name="username" 
                placeholder="Escribe tu usuario" 
                autocomplete="off" required/>
        </div>
        <div class="input-container">
            <input 
                class="form-control" 
                type="password" 
                name="password" 
                placeholder="Escribe tu contraseña" 
                autocomplete="new-password" required/>
        </div>
        <input class="btn btn-primary btn-block" name="login" type="submit" value="Login">
    </form>
    <!-- End Login Form -->
    <!-- Start Signup Form -->
    <form class="signup" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data"> 
        <div class="input-container">
            <input 
                pattern=".{4,8}"
                title="Username tiene que tener menos de 8 caracteres"
                class="form-control" 
                type="text" 
                name="username" 
                placeholder="Escribe tu usuario" 
                autocomplete="off"/>
        </div>
        <div class="input-container">
            <input 
                minlength="4"
                class="form-control" 
                type="password" 
                name="password" 
                placeholder="Escribe tu contraseña" 
                autocomplete="new-password"/>
        </div>
        <div class="input-container">
            <input 
                minlength="4"
                class="form-control" 
                type="password" 
                name="password2" 
                placeholder="Confirma tu contraseña" 
                autocomplete="new-password"/>
        </div>
        <div class="input-container">
            <input 
                class="form-control" 
                type="text" 
                name="email" 
                placeholder="Escribe tu email"/>
        </div>
        <div class="input-container">
            <input type="file"  name="avatar" class="form-control" required="required"/>
        </div>
        <input class="btn btn-success btn-block" name="signup" type="submit" value="Signup">
    </form>
    <!-- End Signup Form -->
    <div class="the-errors text-center">
        <?php
            if(! empty($formErrors)){
                foreach($formErrors as $error){
                    echo '<div class="msg error">' . $error . '</div>';
                }
            }
            if(isset($succesMsg)){
                echo '<div class="msg success ">' . $succesMsg . '</div>';
            }
        ?>
    </div>
</div>


<?php  
    include $tpl . 'footer.php'; 
    ob_end_flush();
?>