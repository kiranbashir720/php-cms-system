<!-- <?php
// test-api.php

$api_key = "abd6bca6750047959ab27e4d626322e0";
$url = "https://newsapi.org/v2/top-headlines?country=us&apiKey=" . $api_key;

echo "<h2>Testing NewsAPI Connection</h2>";
echo "<p>API Key: " . substr($api_key, 0, 10) . "...</p>";
echo "<p>URL: " . $url . "</p>";

// Test with file_get_contents first
echo "<h3>Testing with file_get_contents:</h3>";
$context = stream_context_create([
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
    ]
]);

$response = @file_get_contents($url, false, $context);

if($response === FALSE) {
    echo "<p style='color: red;'>file_get_contents failed</p>";
    
    // Try with cURL
    echo "<h3>Testing with cURL:</h3>";
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ]);
    
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        echo "<p style='color: red;'>cURL Error: " . curl_error($ch) . "</p>";
    } else {
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo "<p>HTTP Code: " . $http_code . "</p>";
        
        if($http_code == 200) {
            $data = json_decode($response, true);
            echo "<pre>";
            print_r($data);
            echo "</pre>";
        } else {
            echo "<p style='color: red;'>Failed with HTTP code: " . $http_code . "</p>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
    }
    
    curl_close($ch);
} else {
    $data = json_decode($response, true);
    echo "<p style='color: green;'>Success! file_get_contents worked</p>";
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

// Test if allow_url_fopen is enabled
echo "<h3>PHP Configuration:</h3>";
echo "<p>allow_url_fopen: " . (ini_get('allow_url_fopen') ? 'Enabled' : 'Disabled') . "</p>";
echo "<p>cURL: " . (function_exists('curl_init') ? 'Enabled' : 'Disabled') . "</p>";
?> -->