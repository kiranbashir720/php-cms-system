<?php
include "config.php";

$apiKey = "abd6bca6750047959ab27e4d626322e0";

// NewsAPI categories
$categories = ['general','sports','technology','health','business'];

// HTTP context with User-Agent (NewsAPI sometimes requires user-agent)
$context = stream_context_create([
    "http" => [
        "header" => "User-Agent: Mozilla/5.0\r\n"
    ]
]);

foreach($categories as $cat){

    // API URL with pageSize increased to 25
    $url = "https://newsapi.org/v2/top-headlines?country=us&category={$cat}&pageSize=25&apiKey={$apiKey}";
    
    $response = @file_get_contents($url, false, $context);
    if(!$response) continue; // If API call fails, skip

    $data = json_decode($response, true);

    if($data['status'] == 'ok'){

        // Check if category exists, otherwise insert
        $checkCat = mysqli_query($conn, "SELECT * FROM category WHERE category_name = '{$cat}'");
        if(mysqli_num_rows($checkCat) == 0){
            mysqli_query($conn, "INSERT INTO category(category_name, post) VALUES('{$cat}',0)");
        }

        $catResult = mysqli_query($conn, "SELECT category_id FROM category WHERE category_name = '{$cat}'");
        $catRow = mysqli_fetch_assoc($catResult);
        $cat_id = $catRow['category_id'];

        foreach($data['articles'] as $article){

            $title = mysqli_real_escape_string($conn, $article['title'] ?? '');
            $desc  = mysqli_real_escape_string($conn, $article['description'] ?? '');
            $imageUrl = $article['urlToImage'] ?? '';

            // Only insert posts which have image, title, and description
            if(empty($title) || empty($desc) || empty($imageUrl)){
                continue;
            }

            // Check for duplicate by title
            $dup = mysqli_query($conn, "SELECT * FROM post WHERE title = '{$title}'");
            if(mysqli_num_rows($dup) == 0){

                // Insert post into DB, save the image URL (do not download)
                mysqli_query($conn,"INSERT INTO post(title, description, category, post_date, author, post_img)
                VALUES('{$title}','{$desc}',{$cat_id},NOW(),1,'{$imageUrl}')");

                // Update category post count
                mysqli_query($conn,"UPDATE category SET post = post + 1 WHERE category_id = {$cat_id}");
            }
        }
    }
}

echo "News Imported Successfully!";
?>
