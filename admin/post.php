<?php 
include "header.php"; 

if($_SESSION["user_role"] == '0'){
  header("Location: {$hostname}/admin/post.php");
}
?>

<div id="admin-content">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1 class="admin-heading">All Posts</h1>

        <a class="add-new" href="add-post.php">Add Post</a>

        <!-- ✅ NEW BUTTON FOR API IMPORT -->
        <?php if($_SESSION["user_role"] == 1){ ?>
          <a class="add-new" href="import-news.php" style="background:#28a745;">Import API News</a>
        <?php } ?>

      </div>

      <div class="col-md-12">
        <?php
          include "config.php";

          $limit = 10;

          if(isset($_GET['page'])){
            $page = $_GET['page'];
          }else{
            $page = 1;
          }

          $offset = ($page - 1) * $limit;

          if($_SESSION["user_role"] == '1'){
            $sql = "SELECT post.post_id, post.title, category.category_name,
                    user.username, post.post_date
                    FROM post
                    LEFT JOIN category ON post.category = category.category_id
                    LEFT JOIN user ON post.author = user.user_id
                    ORDER BY post.post_id DESC LIMIT {$offset},{$limit}";
          }else{
            $sql = "SELECT post.post_id, post.title, category.category_name,
                    user.username, post.post_date
                    FROM post
                    LEFT JOIN category ON post.category = category.category_id
                    LEFT JOIN user ON post.author = user.user_id
                    WHERE post.author = {$_SESSION['user_id']}
                    ORDER BY post.post_id DESC LIMIT {$offset},{$limit}";
          }

          $result = mysqli_query($conn,$sql) or die("Query Failed.");

          if(mysqli_num_rows($result) > 0){
        ?>

        <table class="content-table">
          <thead>
            <th>S.No.</th>
            <th>Title</th>
            <th>Category</th>
            <th>Date</th>
            <th>Author</th>
            <th>Edit</th>
            <th>Delete</th>
          </thead>
          <tbody>

          <?php
            $serial = $offset + 1;
            while($row = mysqli_fetch_assoc($result)) {
          ?>
            <tr>
              <td><?php echo $serial++; ?></td>
              <td><?php echo $row['title']; ?></td>
              <td><?php echo $row['category_name']; ?></td>
              <td><?php echo $row['post_date']; ?></td>
              <td><?php echo $row['username']; ?></td>
              <td class='edit'>
                <a href='update-post.php?id=<?php echo $row['post_id']; ?>'>
                  <i class='fa fa-edit'></i>
                </a>
              </td>
              <td class='delete'>
                <a href='delete-post.php?id=<?php echo $row['post_id']; ?>'>
                  <i class='fa fa-trash-o'></i>
                </a>
              </td>
            </tr>
          <?php } ?>
          </tbody>
        </table>

        <?php
          // Pagination
          $sql1 = "SELECT * FROM post";
          $result1 = mysqli_query($conn,$sql1) or die("Query Failed.");
          if(mysqli_num_rows($result1) > 0){
            $total_records = mysqli_num_rows($result1);
            $total_page = ceil($total_records / $limit);

            echo '<ul class="pagination admin-pagination">';
            if($page > 1){
              echo '<li><a href="post.php?page='.($page-1).'">Prev</a></li>';
            }

            for($i = 1; $i <= $total_page; $i++){
              if($i == $page){
                $active = "class='active'";
              }else{
                $active = "";
              }
              echo '<li '.$active.'><a href="post.php?page='.$i.'">'.$i.'</a></li>';
            }

            if($total_page > $page){
              echo '<li><a href="post.php?page='.($page+1).'">Next</a></li>';
            }
            echo '</ul>';
          }

        }else{
          echo "<h2>No Posts Found.</h2>";
        }
        ?>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
