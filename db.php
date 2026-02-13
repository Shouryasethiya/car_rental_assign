<?php
session_start();

$host = '127.0.0.1'; 

$user = 'root'; 
$pass = '';    
$db   = 'car_rental_db';
$port = 3307; 

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 

try {
    $conn = new mysqli($host, $user, $pass, $db, $port);
} catch (mysqli_sql_exception $e) {
    die("Connection failed: " . $e->getMessage());
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}
?>