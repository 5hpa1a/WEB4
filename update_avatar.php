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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['new_avatar'])) {
    $user_id = $_SESSION['user_id'];
    
    if ($_FILES['new_avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (in_array($_FILES['new_avatar']['type'], $allowed_types)) {
            $upload_dir = 'uploads/avatars/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $filename = uniqid() . '_' . $_FILES['new_avatar']['name'];
            $upload_file = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['new_avatar']['tmp_name'], $upload_file)) {
                // Обновление фото в БД
                $update_query = "UPDATE Пользователи SET photo = ? WHERE id = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("si", $filename, $user_id);
                
                if ($stmt->execute()) {
                    $success = true;
                    
                    // Удаление старого аватара, если он существует
                    $query = "SELECT photo FROM Пользователи WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows === 1) {
                        $user = $result->fetch_assoc();
                        if (!empty($user['photo']) && $user['photo'] !== $filename) {
                            $old_avatar = $upload_dir . $user['photo'];
                            if (file_exists($old_avatar)) {
                                unlink($old_avatar);
                            }
                        }
                    }
                } else {
                    $errors[] = "Ошибка обновления фото в базе данных: " . $conn->error;
                }
            } else {
                $errors[] = "Ошибка при загрузке фото";
            }
        } else {
            $errors[] = "Недопустимый тип файла. Разрешены только JPG, PNG и GIF";
        }
    } else {
        $errors[] = "Ошибка загрузки файла: " . $_FILES['new_avatar']['error'];
    }
}

// Перенаправление обратно в профиль
if ($success) {
    $_SESSION['avatar_success'] = "Аватар успешно обновлен";
} elseif (!empty($errors)) {
    $_SESSION['avatar_errors'] = $errors;
}

header("Location: profile.php#settings");
exit;
?>
