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

$email = 'commenter_' . rand(1, 10000) . '@example.com';
req('/register', 'POST', [
    'name' => 'Commenter',
    'email' => $email,
    'password' => 'password',
    'password_confirmation' => 'password'
]);

$login = req('/login', 'POST', ['email' => $email, 'password' => 'password']);
$token = $login['access_token'] ?? null;

if (!$token) {
    echo "NO TOKEN GENERATED - Cannot proceed.\n";
    exit;
}

$post = req('/posts', 'POST', [
    'title' => 'Post to comment on',
    'content' => 'We need comments here!'
], $token);
$postId = $post['post']['id'] ?? null;

if ($postId) {
    $comment = req("/posts/{$postId}/comments", 'POST', [
        'content' => 'This is an amazing post!'
    ], $token);
    $commentId = $comment['comment']['id'] ?? null;

    req("/posts/{$postId}/comments", 'GET');
    req("/posts/{$postId}", 'GET'); // Should include comments now

    if ($commentId) {
        req("/comments/{$commentId}", 'PUT', [
            'content' => 'This post is just okay.'
        ], $token);

        req("/comments/{$commentId}", 'DELETE', null, $token);
    }
}
