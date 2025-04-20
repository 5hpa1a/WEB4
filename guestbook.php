<?php
include 'header.php';

$errors = [];
$success = false;

// Добавление отзыва
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $rating = (int)$_POST['rating'];
    $review = trim($_POST['review']);
    
    // Проверка полей
    if (empty($name)) {
        $errors[] = "Пожалуйста, введите ваше имя";
    }
    
    if ($rating < 1 || $rating > 5) {
        $errors[] = "Пожалуйста, выберите рейтинг от 1 до 5";
    }
    
    if (empty($review)) {
        $errors[] = "Пожалуйста, введите текст отзыва";
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO Отзывы (имя, рейтинг, отзыв, дата) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sis", $name, $rating, $review);
        
        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Ошибка при добавлении отзыва: " . $conn->error;
        }
    }
}

// Получение всех отзывов
$query = "SELECT * FROM Отзывы ORDER BY дата DESC";
$result = $conn->query($query);
?>

<div class="guestbook-container">
    <h1 class="section-title">Гостевая книга</h1>
    
    <div class="guestbook-intro">
        <p>Здесь вы можете поделиться своим опытом работы с нашей компанией, оставить отзыв о наших товарах или услугах. Мы ценим мнение каждого клиента!</p>
    </div>
    
    <div class="guestbook-form-container">
        <h2>Оставить отзыв</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                Спасибо! Ваш отзыв успешно добавлен.
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
        
        <form method="post" action="" class="guestbook-form">
            <div class="form-group">
                <label for="name">Ваше имя *</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Оценка *</label>
                <div class="rating">
                    <input type="radio" id="star5" name="rating" value="5">
                    <label for="star5" title="Отлично">5 звезд</label>
                    <input type="radio" id="star4" name="rating" value="4">
                    <label for="star4" title="Хорошо">4 звезды</label>
                    <input type="radio" id="star3" name="rating" value="3">
                    <label for="star3" title="Нормально">3 звезды</label>
                    <input type="radio" id="star2" name="rating" value="2">
                    <label for="star2" title="Плохо">2 звезды</label>
                    <input type="radio" id="star1" name="rating" value="1">
                    <label for="star1" title="Ужасно">1 звезда</label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="review">Отзыв *</label>
                <textarea id="review" name="review" class="form-control" rows="5" required></textarea>
            </div>
            
            <button type="submit" class="btn">Отправить отзыв</button>
        </form>
    </div>
    
    <div class="guestbook-entries">
        <h2>Отзывы наших клиентов</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="guestbook-entry">
                    <div class="entry-header">
                        <div class="entry-author"><?php echo htmlspecialchars($row['имя']); ?></div>
                        <div class="entry-date"><?php echo date('d.m.Y', strtotime($row['дата'])); ?></div>
                    </div>
                    <div class="entry-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star<?php echo ($i <= $row['рейтинг']) ? '' : '-o'; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <div class="entry-content">
                        <?php echo nl2br(htmlspecialchars($row['отзыв'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-reviews">
                <p>Пока отзывов нет. Будьте первым, кто оставит отзыв!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingInputs = document.querySelectorAll('.rating input');
    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Обновление визуального отображения рейтинга
            const value = this.value;
            ratingInputs.forEach(inp => {
                const label = document.querySelector(`label[for="${inp.id}"]`);
                if (inp.value <= value) {
                    label.classList.add('selected');
                } else {
                    label.classList.remove('selected');
                }
            });
        });
    });
});
</script>

<?php include 'footer.php'; ?>
