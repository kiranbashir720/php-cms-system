<?php
include "config.php";

$page = basename($_SERVER['PHP_SELF']);

$sql_title = "SELECT websitename FROM settings";
$result_title = mysqli_query($conn, $sql_title);
$row_title = mysqli_fetch_assoc($result_title);
$page_title = $row_title['websitename'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $page_title; ?></title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="css/font-awesome.css">
<link rel="stylesheet" href="css/style.css">

<style>
/* ===== NAVBAR STYLING ===== */
.navbar-custom {
    background: #1565c0;
    border: none;
    border-radius: 0;
    margin-bottom: 0;
    min-height: 70px; /* navbar height */
}

.navbar-header, .navbar-brand {
    display: flex !important;
    align-items: center !important; /* vertical center */
    height: 70px; /* fill navbar height */
    padding: 0;
}

.navbar-brand img {
    max-height: 50px;
    height: auto;
    width: auto;
}

.navbar-custom .navbar-nav > li > a {
    color: #fff;
    font-size: 15px;
    font-weight: 500;
    line-height: normal;
    padding-top: 20px;
    padding-bottom: 20px;
}

.navbar-custom .navbar-nav > li > a:hover {
    background: #0d47a1;
    color: #fff;
}

.navbar-collapse {
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

@media (max-width: 991px) {
    .navbar-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between;
        width: 100%;
    }
    .navbar-brand {
        display: flex !important;
        align-items: center !important;
        height: 70px;
        padding: 0;
    }
    .navbar-toggle {
        order: 2;
        margin-right: 0;
    }
    .navbar-collapse {
        display: block;
        margin-top: 10px;
    }
    .navbar-nav {
        float: none;
        width: 100%;
        text-align: right;
    }
    .navbar-nav > li {
        display: inline-block;
    }
    .navbar-nav > li > a {
        padding-top: 10px;
        padding-bottom: 10px;
    }
}

.navbar-toggle .icon-bar {
    background: #fff;
}
</style>
</head>
<body>

<nav class="navbar navbar-custom">
  <div class="container">

    <div class="navbar-header">

      <!-- Logo -->
      <?php
        $sql = "SELECT * FROM settings";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
      ?>

      <a class="navbar-brand" href="index.php">
        <?php 
          if($row['logo'] == ""){
              echo $row['websitename'];
          } else {
              echo "<img src='admin/images/{$row['logo']}' alt='logo'>";
          }
        ?>
      </a>

      <!-- Mobile Button -->
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#mainNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>

    </div>

    <!-- Links Right -->
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="index.php">Home</a></li>

        <?php
        // Only show categories that have posts > 0
        $cat_sql = "SELECT category.*, COUNT(post.post_id) as post_count 
                    FROM category 
                    LEFT JOIN post ON category.category_id = post.category 
                    GROUP BY category.category_id 
                    HAVING post_count > 0 
                    ORDER BY category.category_name ASC";
        $cat_result = mysqli_query($conn, $cat_sql);

        if(mysqli_num_rows($cat_result) > 0){
            while($cat_row = mysqli_fetch_assoc($cat_result)){
                echo "<li><a href='category.php?cid={$cat_row['category_id']}'>{$cat_row['category_name']}</a></li>";
            }
        }
        ?>
      </ul>
    </div>

  </div>
</nav>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>