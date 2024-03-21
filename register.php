<?php

include 'config.php';

if(isset($_POST['submit'])) {
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = $_POST['password']; // No necesitas escapar esto si vas a hashear la contraseña con password_hash()
   $cpass = $_POST['cpassword']; // No necesitas escapar esto si vas a hashear la contraseña con password_hash()
   $user_type = $_POST['user_type'];

   // Verificar si el usuario ya existe
   $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'") or die('query failed');
   if(mysqli_num_rows($select_users) > 0) {
      $message[] = 'User already exists!';
   } else {
      if($pass != $cpass) {
         $message[] = 'Confirm password not matched!';
      } else {
         // Hashear la contraseña antes de almacenarla
         $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
         mysqli_query($conn, "INSERT INTO `users`(name, email, password, user_type) VALUES('$name', '$email', '$hashed_password', '$user_type')") or die('query failed');
         $message[] = 'Registered successfully!';
         header('Location: login.php');
         exit;
      }
   }
}

if (isset($_POST['action']) && $_POST['action'] == 'login') {
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $password = $_POST['password'];

   // Buscar el usuario por correo electrónico
   $query = "SELECT * FROM `users` WHERE email='$email'";
   $result = mysqli_query($conn, $query);
   
   if(mysqli_num_rows($result) == 1) {
      $user = mysqli_fetch_assoc($result);
      // Verificar la contraseña
      if(password_verify($password, $user['password'])) {
         // Iniciar sesión exitosamente
         // Aquí puedes establecer las variables de sesión y redirigir al usuario a la página de inicio
         header('Location: welcome.php');
         exit;
      } else {
         $message[] = 'Incorrect email or password!';
      }
   } else {
      $message[] = 'Incorrect email or password!';
   }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>



<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>
   
<div class="form-container">

   <form action="" method="post">
      <h3>register now</h3>
      <input type="text" name="name" placeholder="enter your name" required class="box">
      <input type="email" name="email" placeholder="enter your email" required class="box">
      <input type="password" name="password" placeholder="enter your password" required class="box">
      <input type="password" name="cpassword" placeholder="confirm your password" required class="box">
      <select name="user_type" class="box">
         <option value="user">user</option>
         <option value="admin">admin</option>
      </select>
      <input type="submit" name="submit" value="register now" class="btn">
      <p>already have an account? <a href="login.php">login now</a></p>
   </form>

</div>

</body>
</html>