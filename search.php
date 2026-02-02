<?php include 'header.php'; ?>
    <div id="main-content">
      <div class="container">
        <div class="row">
            <div class="col-md-8">
                <!-- post-container -->
                <div class="post-container">
                  <?php
                  include "config.php";
                  
                  if(isset($_GET['search']) && !empty($_GET['search'])){
                    $search_term = mysqli_real_escape_string($conn, $_GET['search']);
                    
                    // Clean and prepare search terms
                    $search_term_original = $search_term;
                    $search_terms = explode(' ', $search_term);
                    
                    // Remove empty terms
                    $search_terms = array_filter(array_map('trim', $search_terms));
                    
                  ?>
                  
                  <div class="search-header">
                      <h2 class="page-heading">Search Results for: "<?php echo htmlspecialchars($search_term_original); ?>"</h2>
                      <?php
                      // Count results first (simpler query)
                      $count_sql = "SELECT COUNT(*) as total FROM post 
                                    WHERE post.title LIKE '%{$search_term}%' 
                                    OR post.description LIKE '%{$search_term}%'";
                      $count_result = mysqli_query($conn, $count_sql);
                      if($count_result) {
                          $count_row = mysqli_fetch_assoc($count_result);
                          echo '<p class="search-count">' . $count_row['total'] . ' results found</p>';
                      }
                      ?>
                  </div>
                  
                  <?php

                    /* Calculate Offset Code */
                    $limit = 3;
                    if(isset($_GET['page'])){
                      $page = $_GET['page'];
                    }else{
                      $page = 1;
                    }
                    $offset = ($page - 1) * $limit;

                    // Improved but simpler search query
                    $sql = "SELECT post.post_id, post.title, post.description, post.post_date, post.author,
                    category.category_name, user.username, post.category, post.post_img
                    
                    FROM post
                    LEFT JOIN category ON post.category = category.category_id
                    LEFT JOIN user ON post.author = user.user_id
                    WHERE post.title LIKE '%{$search_term}%' 
                    OR post.description LIKE '%{$search_term}%'
                    
                    ORDER BY 
                    CASE 
                        WHEN post.title LIKE '%{$search_term}%' THEN 1
                        ELSE 2
                    END,
                    post.post_id DESC 
                    LIMIT {$offset},{$limit}";

                    $result = mysqli_query($conn, $sql) or die("Query Failed: " . mysqli_error($conn));
                    
                    if(mysqli_num_rows($result) > 0){
                      while($row = mysqli_fetch_assoc($result)) {
                          
                          // Highlight search terms in title
                          $highlighted_title = $row['title'];
                          $highlighted_desc = substr($row['description'], 0, 200) . "...";
                          
                          // Check if matched in title or description
                          $matched_in = '';
                          if(stripos($row['title'], $search_term) !== false) {
                              $matched_in = 'title';
                          } else if(stripos($row['description'], $search_term) !== false) {
                              $matched_in = 'description';
                          }
                          
                          // Highlight the search term
                          if(!empty($search_term)) {
                              $highlighted_title = preg_replace(
                                  "/(" . preg_quote($search_term, '/') . ")/i", 
                                  '<span class="highlight">$1</span>', 
                                  $highlighted_title
                              );
                              
                              $highlighted_desc = preg_replace(
                                  "/(" . preg_quote($search_term, '/') . ")/i", 
                                  '<span class="highlight">$1</span>', 
                                  $highlighted_desc
                              );
                          }
                          
                          // Handle image path
                          $imagePath = "images/no-image.png";
                          if(!empty($row['post_img'])){
                              if(filter_var($row['post_img'], FILTER_VALIDATE_URL)){
                                  $imagePath = $row['post_img']; // API image
                              } else {
                                  $imagePath = "admin/upload/" . $row['post_img']; // Admin uploaded
                              }
                          }
                  ?>
                  
                    <div class="post-content search-result">
                        <?php if($matched_in): ?>
                        <div class="match-info">
                            <span class="badge match-badge">
                                <i class="fa fa-search"></i> 
                                Matched in: <?php echo ucfirst($matched_in); ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-4">
                              <a class="post-img" href="single.php?id=<?php echo $row['post_id']; ?>">
                                  <img src="<?php echo $imagePath; ?>" alt=""/>
                              </a>
                            </div>
                            <div class="col-md-8">
                              <div class="inner-content clearfix">
                                  <h3>
                                      <a href='single.php?id=<?php echo $row['post_id']; ?>'>
                                          <?php echo $highlighted_title; ?>
                                      </a>
                                  </h3>
                                  
                                  <div class="post-information">
                                      <span>
                                          <i class="fa fa-tags" aria-hidden="true"></i>
                                          <a href='category.php?cid=<?php echo $row['category']; ?>'>
                                              <?php echo $row['category_name']; ?>
                                          </a>
                                      </span>
                                      <span>
                                          <i class="fa fa-user" aria-hidden="true"></i>
                                          <a href='author.php?aid=<?php echo $row['author']; ?>'>
                                              <?php echo $row['username']; ?>
                                          </a>
                                      </span>
                                      <span>
                                          <i class="fa fa-calendar" aria-hidden="true"></i>
                                          <?php echo $row['post_date']; ?>
                                      </span>
                                  </div>
                                  
                                  <p class="description">
                                      <?php echo $highlighted_desc; ?>
                                  </p>
                                  
                                  <a class='read-more pull-right' href='single.php?id=<?php echo $row['post_id']; ?>'>
                                      <i class="fa fa-arrow-right"></i> Read full article
                                  </a>
                              </div>
                            </div>
                        </div>
                    </div>
                    <?php
                      }
                    } else {
                      // Try alternative search if no results found
                      echo "<div class='no-results'>";
                      echo "<h2>No matches found for: \"$search_term\"</h2>";
                      
                      // Search for similar words
                      $similar_sql = "SELECT DISTINCT title FROM post 
                                     WHERE (title LIKE '%" . substr($search_term, 0, 3) . "%' 
                                     OR description LIKE '%" . substr($search_term, 0, 3) . "%')
                                     LIMIT 5";
                      $similar_result = mysqli_query($conn, $similar_sql);
                      
                      if(mysqli_num_rows($similar_result) > 0) {
                          echo "<p class='suggestions'>Try searching for:</p>";
                          echo "<ul class='suggestion-list'>";
                          while($similar = mysqli_fetch_assoc($similar_result)) {
                              $suggest_title = $similar['title'];
                              // Extract first few words as suggestion
                              $words = explode(' ', $suggest_title);
                              $suggestion = implode(' ', array_slice($words, 0, 3));
                              if(count($words) > 3) $suggestion .= '...';
                              
                              echo "<li><a href='search.php?search=" . urlencode($suggestion) . "'>" . 
                                   htmlspecialchars($suggestion) . "</a></li>";
                          }
                          echo "</ul>";
                      }
                      
                      echo "</div>";
                    }

                    // Pagination
                    $total_sql = "SELECT COUNT(*) as total FROM post 
                                 WHERE post.title LIKE '%{$search_term}%' 
                                 OR post.description LIKE '%{$search_term}%'";
                    
                    $total_result = mysqli_query($conn, $total_sql) or die("Query Failed.");
                    $total_row = mysqli_fetch_assoc($total_result);
                    
                    if($total_row['total'] > 0){

                      $total_records = $total_row['total'];
                      $total_page = ceil($total_records / $limit);

                      echo '<ul class="pagination admin-pagination">';
                      
                      // Previous button
                      if($page > 1){
                        echo '<li><a href="search.php?search='.urlencode($search_term).'&page='.($page - 1).'">
                                <i class="fa fa-angle-left"></i> Prev
                              </a></li>';
                      }
                      
                      // Page numbers
                      for($i = 1; $i <= $total_page; $i++){
                        if($i == $page){
                          $active = "active";
                        }else{
                          $active = "";
                        }
                        echo '<li class="'.$active.'">
                                <a href="search.php?search='.urlencode($search_term).'&page='.$i.'">'.$i.'</a>
                              </li>';
                      }
                      
                      // Next button
                      if($total_page > $page){
                        echo '<li><a href="search.php?search='.urlencode($search_term).'&page='.($page + 1).'">
                                Next <i class="fa fa-angle-right"></i>
                              </a></li>';
                      }

                      echo '</ul>';
                    }
                    
                  } else {
                    echo "<div class='no-search-term'>
                            <h2>Search Page</h2>
                            <p>Please enter a search term in the search box.</p>
                            <p>Try searching for news, articles, or specific topics.</p>
                          </div>";
                  }
                    ?>
                </div><!-- /post-container -->
            </div>
            <?php include 'sidebar.php'; ?>
        </div>
      </div>
    </div>
    
<?php include 'footer.php'; ?>

<style>
/* Search page specific styles */
.search-header {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 30px;
    border-left: 5px solid #1565c0;
}

.search-count {
    color: #666;
    font-size: 16px;
    margin-top: 10px;
}

.highlight {
    background-color: #fff3cd;
    color: #856404;
    padding: 2px 5px;
    border-radius: 3px;
    font-weight: bold;
}

.search-result {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 25px;
    transition: all 0.3s ease;
}

.search-result:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-color: #1565c0;
}

.match-info {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px dashed #ddd;
}

.match-badge {
    background: #28a745;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
}

.no-results {
    text-align: center;
    padding: 40px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.suggestions {
    color: #666;
    margin: 20px 0 10px 0;
}

.suggestion-list {
    list-style: none;
    padding: 0;
    max-width: 500px;
    margin: 0 auto;
}

.suggestion-list li {
    padding: 8px 15px;
    margin: 5px 0;
    background: #f8f9fa;
    border-radius: 4px;
    border-left: 4px solid #1565c0;
}

.suggestion-list li a {
    color: #1565c0;
    text-decoration: none;
}

.suggestion-list li a:hover {
    text-decoration: underline;
}

.no-search-term {
    text-align: center;
    padding: 50px;
    background: #f8f9fa;
    border-radius: 8px;
}

.no-search-term h2 {
    color: #1565c0;
    margin-bottom: 15px;
}

.no-search-term p {
    color: #666;
    margin-bottom: 10px;
}

.read-more {
    background: #1565c0;
    color: white;
    padding: 8px 15px;
    border-radius: 4px;
    text-decoration: none;
    display: inline-block;
    transition: background 0.3s;
}

.read-more:hover {
    background: #0d47a1;
    color: white;
    text-decoration: none;
}
</style>