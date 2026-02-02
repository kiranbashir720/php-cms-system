<?php 
include_once 'config.php'; 
include_once 'header.php'; 
?>

<div id="main-content">
    <div class="container">
        <div class="row">

            <!-- Main Content -->
            <div class="col-md-8">
                <div class="post-container">
                    <?php
                    /* Pagination setup */
                    $limit = 3;
                    $page = isset($_GET['page']) ? $_GET['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    $sql = "SELECT post.post_id, post.title, post.description, post.post_date, post.author,
                            category.category_name, user.username, post.category, post.post_img 
                            FROM post
                            LEFT JOIN category ON post.category = category.category_id
                            LEFT JOIN user ON post.author = user.user_id
                            ORDER BY post.post_id DESC LIMIT {$offset}, {$limit}";
                    
                    $result = mysqli_query($conn, $sql) or die("Query Failed.");

                    if(mysqli_num_rows($result) > 0){
                        $animationDelay = 0.1;
                        while($row = mysqli_fetch_assoc($result)) {

                            // -----------------------------
                            // IMAGE HANDLING
                            // -----------------------------
                            if(!empty($row['post_img'])){
                                if(filter_var($row['post_img'], FILTER_VALIDATE_URL)){
                                    $imagePath = $row['post_img']; // API image
                                } else {
                                    $imagePath = "admin/upload/" . $row['post_img']; // Admin uploaded
                                }
                            } else {
                                $imagePath = "images/no-image.png"; // Default image
                            }
                    ?>
                    <!-- Post Card -->
                    <div class="post-content panel panel-default" 
                         style="margin-bottom:30px; box-shadow:0 2px 8px rgba(0,0,0,0.1); 
                                transition: transform 0.3s ease, box-shadow 0.3s ease, opacity 0.6s ease;
                                transform: translateY(15px); opacity:0;
                                animation: fadeInUp 0.6s forwards; animation-delay:<?php echo $animationDelay; ?>s;">
                        <div class="row">
                            <div class="col-sm-4">
                                <a href="single.php?id=<?php echo $row['post_id']; ?>" 
                                   style="display:block; overflow:hidden; height:200px;">
                                    <img src="<?php echo $imagePath; ?>" alt="" 
                                         style="width:100%; height:100%; object-fit:cover; transition: transform 0.4s ease;">
                                </a>
                            </div>
                            <div class="col-sm-8">
                                <div class="inner-content clearfix" style="padding:15px;">
                                    <h3 style="margin-top:0;">
                                        <a href="single.php?id=<?php echo $row['post_id']; ?>" style="color:#1565c0; text-decoration:none;">
                                            <?php echo $row['title']; ?>
                                        </a>
                                    </h3>
                                    <div class="post-information" style="font-size:12px; color:#777; margin-bottom:10px;">
                                        <span>
                                            <i class="fa fa-tags" aria-hidden="true"></i>
                                            <a href="category.php?cid=<?php echo $row['category']; ?>"><?php echo $row['category_name']; ?></a>
                                        </span>
                                        &nbsp;|&nbsp;
                                        <span>
                                            <i class="fa fa-user" aria-hidden="true"></i>
                                            <a href="author.php?aid=<?php echo $row['author']; ?>"><?php echo $row['username']; ?></a>
                                        </span>
                                        &nbsp;|&nbsp;
                                        <span>
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            <?php echo date('F d, Y', strtotime($row['post_date'])); ?>
                                        </span>
                                    </div>
                                    <p class="description" style="color:#333; line-height:1.6;">
                                        <?php echo substr($row['description'],0,150) . "..."; ?>
                                    </p>
                                    <a class="read-more btn btn-primary btn-sm pull-right" 
                                       href="single.php?id=<?php echo $row['post_id']; ?>" 
                                       style="margin-top:10px; transition: transform 0.3s ease, background-color 0.3s ease;"
                                       onmouseover="this.style.transform='scale(1.05)'; this.style.backgroundColor='#0d47a1';" 
                                       onmouseout="this.style.transform='scale(1)'; this.style.backgroundColor='#1565c0';">
                                       Read More
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        $animationDelay += 0.2; // staggered animation
                        }
                    } else {
                        echo "<h2>No Record Found.</h2>";
                    }

                   // Pagination
$sql1 = "SELECT * FROM post";
$result1 = mysqli_query($conn, $sql1) or die("Query Failed.");

if(mysqli_num_rows($result1) > 0){
    $total_records = mysqli_num_rows($result1);
    $total_page = ceil($total_records / $limit);

    echo '<ul class="pagination" style="display:flex; justify-content:center; margin-top:30px;">';

    // Prev button
    if($page > 1){
        echo '<li><a href="index.php?page='.($page - 1).'">Prev</a></li>';
    }

    // Determine start and end page for 3-page window
    $start = max($page - 1, 1);
    $end = min($start + 2, $total_page);

    // Adjust start if at the end
    if(($end - $start) < 2){
        $start = max($end - 2, 1);
    }

    for($i = $start; $i <= $end; $i++){
        $active = ($i == $page) ? "active" : "";
        echo '<li class="'.$active.'" style="margin:0 5px;"><a href="index.php?page='.$i.'" style="padding:8px 12px;">'.$i.'</a></li>';
    }

    // Next button
    if($page < $total_page){
        echo '<li><a href="index.php?page='.($page + 1).'">Next</a></li>';
    }

    echo '</ul>';
}

                    
                    ?>
                </div>
            </div>

            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- Inline Keyframe Animation -->
<style>
@keyframes fadeInUp {
    to { opacity: 1; transform: translateY(0); }
}
.post-content:hover { transform: translateY(-3px); box-shadow:0 6px 15px rgba(0,0,0,0.08); }
.post-content:hover img { transform: scale(1.05); }
</style>
