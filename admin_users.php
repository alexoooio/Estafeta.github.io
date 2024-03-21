<?php

include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit;
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `users` WHERE id = '$delete_id'") or die('query failed');
    header('location:admin_users.php');
    exit;
}

// Aquí se maneja la acción unificada de "update_user"
if (isset($_POST['action']) && $_POST['action'] == 'update_user') {
    $user_id = $_POST['user_id'];
    $new_name = $_POST['new_name'];
    $new_password = $_POST['new_password'];
    $new_role = $_POST['new_role'];
    $new_email = $_POST['new_email']; // Asegúrate de incluir el campo 'new_email' en tu formulario HTML

    if ($_SESSION['admin_id'] == $user_id) {
        $_SESSION['admin_name'] = $new_name;
        $_SESSION['admin_email'] = $new_email;
    }
    
    if (!empty($new_name)) {
        $query = "UPDATE `users` SET name='$new_name' WHERE id='$user_id'";
        mysqli_query($conn, $query) or die(mysqli_error($conn));
    }

    if (!empty($new_password)) {
        $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE `users` SET password='$new_password_hashed' WHERE id='$user_id'";
        mysqli_query($conn, $query) or die(mysqli_error($conn));
    }

    if (!empty($new_role) && in_array($new_role, ['user', 'admin'])) {
        $query = "UPDATE `users` SET user_type='$new_role' WHERE id='$user_id'";
        mysqli_query($conn, $query) or die(mysqli_error($conn));
    }

    if (!empty($new_email)) {
        $query = "UPDATE `users` SET email='$new_email' WHERE id='$user_id'";
        mysqli_query($conn, $query) or die(mysqli_error($conn));
    }

    header('Location: admin_users.php?success=1');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>users</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="users">
   <h1 class="title"> user accounts </h1>
   <div class="box-container">
      <?php
         $select_users = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
         while($fetch_users = mysqli_fetch_assoc($select_users)){
      ?>
      <div class="box">
         <p> user id : <span><?php echo $fetch_users['id']; ?></span> </p>
         <p> username : <span><?php echo $fetch_users['name']; ?></span> </p>
         <p> email : <span><?php echo $fetch_users['email']; ?></span> </p>
         <p> user type : <span style="<?php if($fetch_users['user_type'] == 'admin'){ echo 'color:var(--orange);'; } ?>"><?php echo $fetch_users['user_type']; ?></span> </p>
         <a href="admin_users.php?delete=<?php echo $fetch_users['id']; ?>" onclick="return confirm('delete this user?');" class="delete-btn">delete user</a>
         <!-- Botón para editar usuario -->
         <a href="javascript:void(0);" class="edit-btn">Editar usuario</a>
         <div class="update-form-container" style="display:none;">
            <!-- Formulario unificado para actualizar usuario -->
            <form action="admin_users.php" method="POST">
               <input type="hidden" name="user_id" value="<?php echo $fetch_users['id']; ?>">
               <label>Nombre:</label>
               <input type="text" name="new_name" placeholder="Nuevo nombre">
               <label>Contraseña:</label>
               <input type="password" name="new_password" placeholder="Nueva contraseña">
               <label>Email:</label>
               <input type="email" name="new_email" placeholder="Nuevo correo electrónico">
               <label>Rol:</label>
               <select name="new_role">
                   <option value="">Selecciona un nuevo rol</option>
                   <option value="user">Usuario</option>
                   <option value="admin">Admin</option>
               </select>
               <button type="submit" name="action" value="update_user">Actualizar Usuario</button>
            </form>
         </div>
      </div>
      <?php
         };
      ?>
   </div>
</section>

<script src="js/admin_script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const editButtons = document.querySelectorAll('.edit-btn');

    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            const updateForm = button.nextElementSibling;
            updateForm.style.display = updateForm.style.display === 'none' ? 'block' : 'none';
        });
    });
});
</script>

</body>
</html>

