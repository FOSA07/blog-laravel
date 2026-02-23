<?php
$baseUrl = 'http://localhost:8000/api';

function req($url, $method = 'GET', $data = null, $token = null) {
    global $baseUrl;
    $ch = curl_init($baseUrl . $url);
    $headers = ['Accept: application/json', 'Content-Type: application/json'];
    if ($token) $headers[] = 'Authorization: Bearer ' . $token;
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    echo "\n=== $method $url ===\n";
    $json = json_decode($response, true);
    if ($json) echo json_encode($json, JSON_PRETTY_PRINT) . "\n";
    else echo $response . "\n";
    return $json;
}

// 1. Login user to get token
$login = req('/login', 'POST', ['email' => 'test_user_steady@example.com', 'password' => 'password']);
$token = $login['access_token'] ?? null;

if (!$token) {
    echo "NO TOKEN GENERATED - Cannot proceed.\n";
    exit;
}

// 2. Create Post
$post = req('/posts', 'POST', [
    'title' => 'My First Post',
    'content' => 'This is the content of my first post'
], $token);
$postId = $post['post']['id'] ?? null;

// 3. Read All Posts (Public)
req('/posts', 'GET');

if ($postId) {
    // 4. Read Single Post
    req("/posts/{$postId}", 'GET');
    
    // 5. Update Post
    req("/posts/{$postId}", 'PUT', [
        'title' => 'My First Post (Updated)',
        'content' => 'The content has changed!'
    ], $token);
    
    // 6. Test Unauthenticated Update (Should Fail)
    req("/posts/{$postId}", 'PUT', ['title' => 'Hacked Details']);
    
    // 7. Delete Post
    req("/posts/{$postId}", 'DELETE', null, $token);
    
    // 8. Verify Deletion
    req("/posts/{$postId}", 'GET');
}

echo "\nDone!\n";
