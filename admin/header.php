<?php
include "config.php";
session_start();
if(!isset($_SESSION["username"])){
    header("Location:{$hostname}/admin/");
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ADMIN Panel</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="../css/font-awesome.css">
    <!-- Custom stylesheet -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<!-- HEADER -->
<!-- HEADER -->
<div id="header-admin" style="background:#1565c0; padding:15px 0;">
    <div class="container">
        <div class="row" style="align-items:center; display:flex;">

            <!-- LOGO -->
            <div class="col-md-2">
                <a href="post.php">
                    <img class="logo" src="images/logo3.jpg" style="max-height:50px; width:auto;">
                </a>
            </div>
            <!-- /LOGO -->

            <!-- Welcome & Logout -->
            <div class="col-md-offset-6 col-md-4" style="text-align:right;">
                <span style="color:#fff; font-weight:600; font-size:16px; margin-right:15px;">
                    Welcome, <?php echo $_SESSION["username"]; ?>
                </span>
                <a href="logout.php" 
                   style="background:#ff5722; color:#fff; padding:8px 18px; border-radius:4px; font-weight:600; text-decoration:none; transition:0.3s;"
                   onmouseover="this.style.background='#e64a19';" 
                   onmouseout="this.style.background='#ff5722';">
                   Logout
                </a>
            </div>

        </div>
    </div>
</div>
<!-- /HEADER -->

<!-- /HEADER -->
 <!-- Menu Bar -->
<div id="admin-menubar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ul class="admin-menu">

                    <!-- Post (sab ko nazar aayega) -->
                    <li><a href="post.php">Post</a></li>

                    <?php
                    // Sirf Admin ke liye
                    if($_SESSION["user_role"] == 1){
                    ?>
                        <li><a href="category.php">Category</a></li>
                        <li><a href="users.php">Users</a></li>
                        <li><a href="settings.php">Settings</a></li>
                    <?php } ?>

                </ul>
            </div>
        </div>
    </div>
</div>
<!-- /Menu Bar -->


