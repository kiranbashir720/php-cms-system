<?php include "header.php"; ?>
<div id="admin-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="admin-heading">Add New Post</h1>
            </div>
            <div class="col-md-offset-3 col-md-6">
                <!-- Form -->
                <form action="save-post.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="post_title">Title</label>
                        <input type="text" name="post_title" id="post_title" class="form-control" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                        <label for="postdesc">Description</label>
                        <textarea name="postdesc" id="postdesc" class="form-control" rows="5" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select name="category" id="category" class="form-control" required>
                            <option disabled selected>Select Category</option>
                            <?php
                                include "config.php"; // include DB connection if not in header
                                $sql = "SELECT * FROM category";
                                $result = mysqli_query($conn, $sql) or die("Query Failed.");
                                if(mysqli_num_rows($result) > 0){
                                    while($row = mysqli_fetch_assoc($result)){
                                        echo "<option value='{$row['category_id']}'>{$row['category_name']}</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fileToUpload">Post Image</label>
                        <input type="file" name="fileToUpload" id="fileToUpload" required>
                    </div>

                    <input type="submit" name="submit" class="btn btn-primary" value="Save">
                </form>
                <!-- /Form -->
            </div>
        </div>
    </div>
</div>
<?php include "footer.php"; ?>
