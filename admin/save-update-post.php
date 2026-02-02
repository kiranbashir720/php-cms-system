<?php
include "config.php";
session_start();

$post_id      = $_POST['post_id'];
$title        = mysqli_real_escape_string($conn,$_POST['post_title']);
$desc         = mysqli_real_escape_string($conn,$_POST['postdesc']);
$new_cat      = $_POST['category'];
$old_cat      = $_POST['old_category'];
$date         = date("Y-m-d H:i:s");

/* IMAGE HANDLING */
if(!empty($_FILES['new-image']['name'])){
    $img_name = time()."-".$_FILES['new-image']['name'];
    move_uploaded_file($_FILES['new-image']['tmp_name'], "upload/".$img_name);
    unlink("upload/".$_POST['old_image']);
}else{
    $img_name = $_POST['old_image'];
}

/* UPDATE POST */
$updatePost = "UPDATE post SET 
               title='{$title}',
               description='{$desc}',
               category={$new_cat},
               post_date='{$date}',
               post_img='{$img_name}'
               WHERE post_id={$post_id}";

if(mysqli_query($conn,$updatePost)){

    /* CATEGORY COUNT SYNC */
    if($old_cat != $new_cat){

        mysqli_query($conn,
            "UPDATE category 
             SET post = IF(post > 0, post - 1, 0) 
             WHERE category_id = {$old_cat}"
        );

        mysqli_query($conn,
            "UPDATE category 
             SET post = post + 1 
             WHERE category_id = {$new_cat}"
        );
    }

    header("Location: {$hostname}/admin/post.php");
}else{
    echo "Update Failed";
}
?>
