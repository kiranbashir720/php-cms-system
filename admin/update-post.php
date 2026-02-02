<?php
include "header.php";
include "config.php";

/* =========================
   SESSION CHECK
========================= */
if(!isset($_SESSION['user_id'])){
  header("Location: {$hostname}/admin/index.php");
  exit;
}

$post_id = $_GET['id'];

/* =========================
   NORMAL USER SECURITY
========================= */
if($_SESSION["user_role"] == 0){
  $checkSql = "SELECT author FROM post WHERE post_id = {$post_id}";
  $checkResult = mysqli_query($conn, $checkSql);

  $checkRow = mysqli_fetch_assoc($checkResult);

  if($checkRow['author'] != $_SESSION['user_id']){
    header("Location: {$hostname}/admin/post.php");
    exit;
  }
}
?>

<div id="admin-content">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1 class="admin-heading">Update Post</h1>
      </div>

      <div class="col-md-offset-3 col-md-6">
        <?php
          $sql = "SELECT * FROM post WHERE post_id = {$post_id}";
          $result = mysqli_query($conn, $sql);

          if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
        ?>

        <!-- UPDATE FORM -->
        <form action="save-update-post.php" method="POST" enctype="multipart/form-data">

          <input type="hidden" name="post_id" value="<?php echo $row['post_id']; ?>">
          <input type="hidden" name="old_image" value="<?php echo $row['post_img']; ?>">
          <input type="hidden" name="old_category" value="<?php echo $row['category']; ?>">

          <div class="form-group">
            <label>Title</label>
            <input type="text" name="post_title" class="form-control"
              value="<?php echo $row['title']; ?>" required>
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea name="postdesc" class="form-control" rows="5" required><?php echo $row['description']; ?></textarea>
          </div>

          <div class="form-group">
            <label>Category</label>
            <select name="category" class="form-control" required>
              <?php
                $catSql = "SELECT * FROM category";
                $catResult = mysqli_query($conn, $catSql);
                while($cat = mysqli_fetch_assoc($catResult)){
                  $selected = ($row['category'] == $cat['category_id']) ? "selected" : "";
                  echo "<option {$selected} value='{$cat['category_id']}'>{$cat['category_name']}</option>";
                }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label>Post Image</label><br>
            <input type="file" name="new-image">
            <br><br>
            <img src="upload/<?php echo $row['post_img']; ?>" height="120">
          </div>

          <input type="submit" name="submit" class="btn btn-primary" value="Update Post">
        </form>

        <?php } else {
          echo "<h3>Post not found</h3>";
        } ?>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
