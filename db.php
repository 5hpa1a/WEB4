<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'task_database';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}
?>
