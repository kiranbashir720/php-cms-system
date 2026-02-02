<?php
include "config.php";
session_start();

/* =============================
   FILE UPLOAD CODE
============================= */
$errors = array();
$new_name = ""; // Default if no file uploaded

if(isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['name'] != ""){

    $file_name = $_FILES['fileToUpload']['name'];
    $file_size = $_FILES['fileToUpload']['size'];
    $file_tmp  = $_FILES['fileToUpload']['tmp_name'];

    // FIXED EXTENSION CODE
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $extensions = array("jpeg","jpg","png");

    if(!in_array($file_ext, $extensions)){
        $errors[] = "This extension is not allowed. Please choose a JPG or PNG file.";
    }

    if($file_size > 2097152){
        $errors[] = "File size must be 2MB or less.";
    }

    // Prepare new file name
    $new_name = time() . "-" . basename($file_name);
    $target = "upload/" . $new_name;

    // Move file only if no errors
    if(empty($errors)){
        if(!move_uploaded_file($file_tmp, $target)){
            $errors[] = "Failed to upload file. Please try again.";
        }
    }
}

/* =============================
   DISPLAY ERRORS IF ANY
============================= */
if(!empty($errors)){
    foreach($errors as $error){
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <strong>Error!</strong> $error
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
    }
    // Stop execution so post is not inserted if image is invalid
    return;
}

/* =============================
   INSERT POST DATA
============================= */
$title       = mysqli_real_escape_string($conn, $_POST['post_title']);
$description = mysqli_real_escape_string($conn, $_POST['postdesc']);
$category    = mysqli_real_escape_string($conn, $_POST['category']);
$date        = date("Y-m-d H:i:s"); // MySQL DATETIME format
$author      = $_SESSION['user_id'];

$sql  = "INSERT INTO post(title, description, category, post_date, author, post_img)
         VALUES('{$title}','{$description}',{$category},'{$date}',{$author},'{$new_name}');";

$sql .= "UPDATE category SET post = post + 1 WHERE category_id = {$category}";

if(mysqli_multi_query($conn, $sql)){
    header("Location: {$hostname}/admin/post.php");
    exit();
}else{
    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            <strong>Error!</strong> Query Failed.
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
}
?>
