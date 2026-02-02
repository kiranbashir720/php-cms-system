<?php
include "config.php";
session_start();

if(isset($_SESSION["username"])){
    header("Location: {$hostname}/admin/post.php");
    exit();
}
?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>ADMIN | Login</title>
<link rel="stylesheet" href="../css/bootstrap.min.css" />
</head>

<body>
<div class="container" style="margin-top:100px;">
<div class="row">
<div class="col-md-offset-4 col-md-4">

<h3>Admin Login</h3>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

<div class="form-group">
<label>Username</label>
<input type="text" name="username" class="form-control" required>
</div>

<div class="form-group">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<input type="submit" name="login" class="btn btn-primary btn-block" value="Login">

</form>

<?php
if(isset($_POST['login'])){

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE username = '{$username}'";
    $result = mysqli_query($conn, $sql) or die("Query Failed.");

    if(mysqli_num_rows($result) == 1){

        $row = mysqli_fetch_assoc($result);

        if(password_verify($password, $row['password'])){

            $_SESSION["username"]  = $row['username'];
            $_SESSION["user_id"]   = $row['user_id'];
            $_SESSION["user_role"] = $row['role'];

            header("Location: {$hostname}/admin/post.php");
            exit();

        } else {
            echo '<div class="alert alert-danger">Invalid Password.</div>';
        }

    } else {
        echo '<div class="alert alert-danger">Invalid Username.</div>';
    }
}
?>

</div>
</div>
</div>
</body>
</html>
