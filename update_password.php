<?php
session_start();
include 'db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Проверка текущего пароля
    $user_id = $_SESSION['user_id'];
    $query = "SELECT password FROM Пользователи WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (!password_verify($current_password, $user['password'])) {
            $errors[] = "Неверный текущий пароль";
        }
    } else {
        $errors[] = "Ошибка получения данных пользователя";
    }
    
    // Проверка нового пароля
    if (empty($new_password)) {
        $errors[] = "Введите новый пароль";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "Новый пароль должен содержать не менее 6 символов";
    }
    
    // Проверка подтверждения пароля
    if ($new_password !== $confirm_password) {
        $errors[] = "Пароли не совпадают";
    }
    
    // Если нет ошибок, обновляем пароль
    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $update_query = "UPDATE Пользователи SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Ошибка обновления пароля: " . $conn->error;
        }
    }
}

// Перенаправление обратно в профиль
if ($success) {
    $_SESSION['password_success'] = "Пароль успешно изменен";
} elseif (!empty($errors)) {
    $_SESSION['password_errors'] = $errors;
}

header("Location: profile.php#settings");
exit;
?>
