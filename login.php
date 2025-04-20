<?php
include 'header.php';

$errors = [];

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    
    if (empty($login) || empty($password)) {
        $errors[] = "Введите логин и пароль";
    } else {
        $query = "SELECT * FROM Пользователи WHERE login = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Успешная авторизация
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_login'] = $user['login'];
                
                header("Location: profile.php");
                exit;
            } else {
                $errors[] = "Неверный пароль";
            }
        } else {
            $errors[] = "Пользователь не найден";
        }
    }
}
?>

<div class="form-container">
    <h2 class="section-title">Авторизация</h2>
    
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="post" action="">
        <div class="form-group">
            <label for="login">Логин</label>
            <input type="text" name="login" id="login" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Войти</button>
    </form>
    
    <div class="mt-3 text-center">
        Еще не зарегистрированы? <a href="register.php">Регистрация</a>
    </div>
</div>

<?php include 'footer.php'; ?>
