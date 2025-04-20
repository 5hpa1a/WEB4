<?php
include 'header.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Получение данных пользователя
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM Пользователи WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Получение заказов пользователя
$orders_query = "SELECT З.Номер_заказа, З.Дата_заказа, З.Статус_заказа 
                FROM Заказы З 
                JOIN Заказы_Клиенты ЗК ON З.Номер_заказа = ЗК.Номер_заказа 
                JOIN Клиенты К ON ЗК.ID_Клиента = К.ID_Клиента 
                WHERE К.user_id = ? 
                ORDER BY З.Дата_заказа DESC";
$stmt = $conn->prepare($orders_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<div class="profile-container">
    <h1 class="section-title">Личный кабинет</h1>
    
    <div class="profile-grid">
        <div class="profile-sidebar">
            <div class="profile-avatar">
                <?php if (!empty($user['photo'])): ?>
                    <img src="uploads/avatars/<?php echo $user['photo']; ?>" alt="Аватар" width="200" height="200">
                <?php else: ?>
                    <div class="default-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($user['login']); ?></h3>
            </div>
            
            <ul class="profile-menu">
                <li class="active"><a href="#personal-info"><i class="fas fa-user"></i> Личная информация</a></li>
                <li><a href="#orders"><i class="fas fa-shopping-bag"></i> Мои заказы</a></li>
                <li><a href="#settings"><i class="fas fa-cog"></i> Настройки</a></li>
            </ul>
        </div>
        
        <div class="profile-content">
            <div id="personal-info" class="profile-section active">
                <h2>Личная информация</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Логин:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['login']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Дата регистрации:</span>
                        <span class="info-value"><?php echo date('d.m.Y', strtotime($user['reg_date'])); ?></span>
                    </div>
                </div>
                <a href="#" class="btn btn-edit-profile">Редактировать профиль</a>
            </div>
            
            <div id="orders" class="profile-section">
                <h2>Мои заказы</h2>
                
                <?php if ($orders_result->num_rows > 0): ?>
                    <div class="orders-list">
                        <div class="order-header">
                            <div class="order-number">№ заказа</div>
                            <div class="order-date">Дата</div>
                            <div class="order-status">Статус</div>
                            <div class="order-actions">Действия</div>
                        </div>
                        
                        <?php while ($order = $orders_result->fetch_assoc()): ?>
                            <div class="order-item">
                                <div class="order-number"><?php echo $order['Номер_заказа']; ?></div>
                                <div class="order-date"><?php echo date('d.m.Y', strtotime($order['Дата_заказа'])); ?></div>
                                <div class="order-status">
                                    <span class="status-badge <?php echo strtolower($order['Статус_заказа']); ?>">
                                        <?php echo $order['Статус_заказа']; ?>
                                    </span>
                                </div>
                                <div class="order-actions">
                                    <a href="order_details.php?id=<?php echo $order['Номер_заказа']; ?>" class="btn btn-sm">Детали</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="no-orders">У вас пока нет заказов.</p>
                    <a href="products.php" class="btn">Перейти к каталогу</a>
                <?php endif; ?>
            </div>
            
            <div id="settings" class="profile-section">
                <h2>Настройки аккаунта</h2>
                
                <div class="settings-section">
                    <h3>Изменить пароль</h3>
                    <form action="update_password.php" method="post" class="form">
                        <div class="form-group">
                            <label for="current_password">Текущий пароль</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">Новый пароль</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Подтвердите новый пароль</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn">Изменить пароль</button>
                    </form>
                </div>
                
                <div class="settings-section">
                    <h3>Изменить аватар</h3>
                    <form action="update_avatar.php" method="post" enctype="multipart/form-data" class="form">
                        <div class="form-group">
                            <label for="new_avatar">Выберите изображение</label>
                            <input type="file" id="new_avatar" name="new_avatar" class="form-control" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn">Загрузить аватар</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuItems = document.querySelectorAll('.profile-menu li');
    const sections = document.querySelectorAll('.profile-section');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.querySelector('a').getAttribute('href');
            
            // Активация пункта меню
            menuItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            
            // Активация секции
            sections.forEach(section => {
                section.classList.remove('active');
                if (section.id === target.substring(1)) {
                    section.classList.add('active');
                }
            });
        });
    });
});
</script>

<?php include 'footer.php'; ?>
