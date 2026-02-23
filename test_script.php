<?php
$baseUrl = 'http://localhost:8080/api';

function makeRequest($url, $method = 'GET', $data = null, $token = null) {
    global $baseUrl;
    $ch = curl_init($baseUrl . $url);
    $headers = ['Accept: application/json', 'Content-Type: application/json'];
    
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    echo "\n=== $method $url ===\n";
    echo $response . "\n";
    return json_decode($response, true);
}

// 1. Register
$register = makeRequest('/register', 'POST', [
    'name' => 'John Doe',
    'email' => 'john.doe' . rand(1, 10000) . '@example.com',
    'password' => 'secret123',
    'password_confirmation' => 'secret123'
]);

// 2. Login
$loginEmail = $register['user']['email'] ?? 'john.doe@example.com';
$login = makeRequest('/login', 'POST', [
    'email' => $loginEmail,
    'password' => 'secret123'
]);

// 3. Protected Route
$token = $login['access_token'] ?? null;
if ($token) {
    makeRequest('/user', 'GET', null, $token);
    
    // 4. Logout
    makeRequest('/logout', 'POST', null, $token);
    
    // 5. Protected Route Again (Should fail)
    makeRequest('/user', 'GET', null, $token);
} else {
    echo "NO TOKEN GENERATED.\n";
}

