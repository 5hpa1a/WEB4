<?php
include 'header.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Проверка полей
    if (empty($name)) {
        $errors[] = "Пожалуйста, введите ваше имя";
    }
    
    if (empty($email)) {
        $errors[] = "Пожалуйста, введите ваш email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Пожалуйста, введите корректный email";
    }
    
    if (empty($subject)) {
        $errors[] = "Пожалуйста, укажите тему сообщения";
    }
    
    if (empty($message)) {
        $errors[] = "Пожалуйста, введите текст сообщения";
    }
    
    if (empty($errors)) {
        // Настройки для отправки почты
        $to = "info@optikapro.ru"; // Email получателя
        $headers = "From: $name <$email>" . "\r\n";
        $headers .= "Reply-To: $email" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        
        $email_message = "
            <html>
            <head>
                <title>Новое сообщение с сайта</title>
            </head>
            <body>
                <h2>Новое сообщение с сайта ОптикаПро</h2>
                <p><strong>Имя:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Тема:</strong> $subject</p>
                <p><strong>Сообщение:</strong></p>
                <p>" . nl2br(htmlspecialchars($message)) . "</p>
            </body>
            </html>
        ";
        
        // Отправка письма
        if (mail($to, "Сообщение с сайта: $subject", $email_message, $headers)) {
            $success = true;
            
            // Сохранение сообщения в БД
            $stmt = $conn->prepare("INSERT INTO Сообщения (имя, email, тема, сообщение, дата) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $name, $email, $subject, $message);
            $stmt->execute();
        } else {
            $errors[] = "Ошибка при отправке сообщения. Пожалуйста, попробуйте позже.";
        }
    }
}
?>

<div class="contacts-container">
    <h1 class="section-title">Контакты</h1>
    
    <div class="contacts-grid">
        <div class="contact-info">
            <div class="contact-section">
                <h3><i class="fas fa-map-marker-alt"></i> Адрес</h3>
                <p>г. Москва, ул. Примерная, 123</p>
                <p>Ежедневно: 10:00 - 20:00</p>
            </div>
            
            <div class="contact-section">
                <h3><i class="fas fa-phone"></i> Телефоны</h3>
                <p><a href="tel:+71234567890">+7 (123) 456-78-90</a></p>
                <p><a href="tel:+79876543210">+7 (987) 654-32-10</a></p>
            </div>
            
            <div class="contact-section">
                <h3><i class="fas fa-envelope"></i> Email</h3>
                <p><a href="mailto:info@optikapro.ru">info@optikapro.ru</a></p>
            </div>
            
            <div class="contact-section">
                <h3><i class="fas fa-share-alt"></i> Социальные сети</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-vk"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-telegram"></i></a>
                </div>
            </div>
        </div>
        
        <div class="contact-form-container">
            <h2>Напишите нам</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    Спасибо! Ваше сообщение успешно отправлено. Мы свяжемся с вами в ближайшее время.
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
            
            <form method="post" action="" class="contact-form">
                <div class="form-group">
                    <label for="name">Ваше имя *</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="subject">Тема *</label>
                    <input type="text" id="subject" name="subject" class="form-control" value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="message">Сообщение *</label>
                    <textarea id="message" name="message" class="form-control" rows="5" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-submit">Отправить сообщение</button>
            </form>
        </div>
    </div>
    
    <div class="map-container">
        <h2>Как нас найти</h2>
        <div class="map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2244.397087693403!2d37.6173026761929!3d55.75199998028261!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46b54a50b315e573%3A0xa886bf5a3d9b2e68!2z0JzQvtGB0LrQvtCy0YHQutC40Lkg0JrRgNC10LzQu9GM!5e0!3m2!1sru!2sru!4v1615139283409!5m2!1sru!2sru" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
