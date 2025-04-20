<?php
include 'header.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = trim($_POST['email']);
    $reg_date = date('Y-m-d');
    
    // Проверка логина
    if (empty($login)) {
        $errors[] = "Логин обязателен";
    } elseif (strlen($login) < 3) {
        $errors[] = "Логин должен содержать не менее 3 символов";
    }
    
    // Проверка пароля
    if (empty($password)) {
        $errors[] = "Пароль обязателен";
    } elseif (strlen($password) < 6) {
        $errors[] = "Пароль должен содержать не менее 6 символов";
    }
    
    // Проверка совпадения паролей
    if ($password !== $confirm_password) {
        $errors[] = "Пароли не совпадают";
    }
    
    // Проверка email
    if (empty($email)) {
        $errors[] = "Email обязателен";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Некорректный email";
    }
    
    // Проверка наличия логина в БД
    $check_query = "SELECT * FROM Пользователи WHERE login = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Пользователь с таким логином уже существует";
    }
    
    // Обработка загруженного фото
    $photo = "";
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['photo']['type'], $allowed_types)) {
            $upload_dir = 'uploads/avatars/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $filename = uniqid() . '_' . $_FILES['photo']['name'];
            $upload_file = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_file)) {
                $photo = $filename;
            } else {
                $errors[] = "Ошибка при загрузке фото";
            }
        } else {
            $errors[] = "Недопустимый тип файла. Разрешены только JPG, PNG и GIF";
        }
    }
    
    // Если нет ошибок, сохраняем пользователя
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $insert_query = "INSERT INTO Пользователи (login, password, email, reg_date, photo) 
                         VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sssss", $login, $hashed_password, $email, $reg_date, $photo);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Регистрация успешна! Теперь вы можете войти.";
            header("Location: login.php");
            exit;
        } else {
            $errors[] = "Ошибка регистрации: " . $conn->error;
        }
    }
}
?>

<div class="form-container">
    <h2 class="section-title">Регистрация</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="login">Логин *</label>
            <input type="text" name="login" id="login" class="form-control" value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password">Пароль *</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Подтверждение пароля *</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="photo">Фото профиля</label>
            <input type="file" name="photo" id="photo" class="form-control">
            <small class="form-text text-muted">Необязательно. Допустимые форматы: JPG, PNG, GIF</small>
        </div>
        
        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
    </form>
    
    <div class="mt-3 text-center">
        Уже зарегистрированы? <a href="login.php">Войти</a>
    </div>
</div>

<?php include 'footer.php'; ?>
