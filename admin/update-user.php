<?php 
include "header.php";

if($_SESSION["user_role"] == '0'){
  header("Location: {$hostname}/admin/post.php");
  exit();
}

include "config.php";

if(isset($_POST['submit'])){

    $userid = mysqli_real_escape_string($conn, $_POST['user_id']);
    $fname  = mysqli_real_escape_string($conn, $_POST['f_name']);
    $lname  = mysqli_real_escape_string($conn, $_POST['l_name']);
    $user   = mysqli_real_escape_string($conn, $_POST['username']);
    $role   = mysqli_real_escape_string($conn, $_POST['role']);

    if(!empty($_POST['password'])){
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // ✅ HASH
        $sql = "UPDATE user 
                SET first_name='{$fname}', last_name='{$lname}', username='{$user}', role='{$role}', password='{$password}' 
                WHERE user_id={$userid}";
    } else {
        $sql = "UPDATE user 
                SET first_name='{$fname}', last_name='{$lname}', username='{$user}', role='{$role}' 
                WHERE user_id={$userid}";
    }

    mysqli_query($conn, $sql) or die("Query Failed.");
    header("Location: {$hostname}/admin/users.php");
    exit();
}
?>

<div id="admin-content">
<div class="container">
<div class="row">
<div class="col-md-12">
<h1 class="admin-heading">Modify User Details</h1>
</div>

<div class="col-md-offset-4 col-md-4">

<?php
$user_id = $_GET['id'];
$sql = "SELECT * FROM user WHERE user_id={$user_id}";
$result = mysqli_query($conn, $sql) or die("Query Failed.");

$row = mysqli_fetch_assoc($result);
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" autocomplete="off">

<input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">

<div class="form-group">
<label>First Name</label>
<input type="text" name="f_name" class="form-control" value="<?php echo $row['first_name']; ?>" required>
</div>

<div class="form-group">
<label>Last Name</label>
<input type="text" name="l_name" class="form-control" value="<?php echo $row['last_name']; ?>" required>
</div>

<div class="form-group">
<label>Username</label>
<input type="text" name="username" class="form-control" value="<?php echo $row['username']; ?>" required>
</div>

<div class="form-group">
<label>Password (leave blank to keep current)</label>
<input type="password" name="password" class="form-control">
</div>

<div class="form-group">
<label>User Role</label>
<select class="form-control" name="role">
<option value="0" <?php if($row['role']==0) echo "selected"; ?>>Normal</option>
<option value="1" <?php if($row['role']==1) echo "selected"; ?>>Admin</option>
</select>
</div>

<input type="submit" name="submit" class="btn btn-primary" value="Update">

</form>

</div>
</div>
</div>
</div>

<?php include "footer.php"; ?>
