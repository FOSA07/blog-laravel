<?php
$baseUrl = 'http://localhost:8000/api'; // Going back to sail's port

function p($url, $method = 'GET', $data = null, $token = null) {
    global $baseUrl;
    $ch = curl_init($baseUrl . $url);
    $headers = [
        'Accept: application/json', 
        'Content-Type: application/json'
    ];
    
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
    $json = json_decode($response, true);
    if ($json) {
        echo json_encode($json, JSON_PRETTY_PRINT) . "\n";
        return $json;
    } else {
        echo $response . "\n";
        return null; // The server returned an error (likely HTML)
    }
}

// 1. Register
$register = p('/register', 'POST', [
    'name' => 'John Doe',
    'email' => 'john.doe' . rand(1, 10000) . '@example.com',
    'password' => 'secret123',
    'password_confirmation' => 'secret123'
]);

// 2. Login
$loginEmail = $register['user']['email'] ?? 'john.doe@example.com';
$login = p('/login', 'POST', [
    'email' => $loginEmail,
    'password' => 'secret123'
]);

// 3. Protected Route
$token = $login['access_token'] ?? null;
if ($token) {
    p('/user', 'GET', null, $token);
    
    // 4. Logout
    p('/logout', 'POST', null, $token);
    
    // 5. Protected Route Again (Should fail)
    p('/user', 'GET', null, $token);
} else {
    echo "NO TOKEN GENERATED.\n";
}

