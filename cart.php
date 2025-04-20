<?php
include 'header.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'cart.php';
    header("Location: login.php");
    exit;
}

// Инициализация корзины, если она не существует
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [
        'frames' => [],
        'lenses' => []
    ];
}

// Удаление товара из корзины
if (isset($_GET['remove']) && isset($_GET['type'])) {
    $remove_id = $_GET['remove'];
    $type = $_GET['type'];
    
    if ($type === 'frame' && isset($_SESSION['cart']['frames'][$remove_id])) {
        unset($_SESSION['cart']['frames'][$remove_id]);
    } elseif ($type === 'lens' && isset($_SESSION['cart']['lenses'][$remove_id])) {
        unset($_SESSION['cart']['lenses'][$remove_id]);
    }
}

// Обновление количества товаров
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    if (isset($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $type => $items) {
            foreach ($items as $id => $quantity) {
                $quantity = max(1, (int)$quantity); // Минимум 1 единица товара
                
                if ($type === 'frame' && isset($_SESSION['cart']['frames'][$id])) {
                    $_SESSION['cart']['frames'][$id]['quantity'] = $quantity;
                } elseif ($type === 'lens' && isset($_SESSION['cart']['lenses'][$id])) {
                    $_SESSION['cart']['lenses'][$id]['quantity'] = $quantity;
                }
            }
        }
    }
}

// Оформление заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    // Проверка, что корзина не пуста
    $cart_empty = empty($_SESSION['cart']['frames']) && empty($_SESSION['cart']['lenses']);
    
    if (!$cart_empty) {
        $user_id = $_SESSION['user_id'];
        $order_date = date('Y-m-d');
        $order_status = 'В обработке';
        
        // Получение данных клиента
        $client_query = "SELECT ID_Клиента FROM Клиенты WHERE user_id = ?";
        $stmt = $conn->prepare($client_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $client_result = $stmt->get_result();
        
        if ($client_result->num_rows > 0) {
            $client = $client_result->fetch_assoc();
            $client_id = $client['ID_Клиента'];
        } else {
            // Если клиент не найден, используем данные из формы для создания
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $phone = $_POST['phone'];
            
            $insert_client = "INSERT INTO Клиенты (Фамилия, Имя, Номер_телефона, user_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_client);
            $stmt->bind_param("sssi", $last_name, $first_name, $phone, $user_id);
            $stmt->execute();
            $client_id = $conn->insert_id;
        }
        
        // Создание заказа
        $insert_order = "INSERT INTO Заказы (Дата_заказа, Статус_заказа) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_order);
        $stmt->bind_param("ss", $order_date, $order_status);
        $stmt->execute();
        $order_id = $conn->insert_id;
        
        // Связь заказа с клиентом
        $insert_order_client = "INSERT INTO Заказы_Клиенты (Номер_заказа, ID_Клиента) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_order_client);
        $stmt->bind_param("ii", $order_id, $client_id);
        $stmt->execute();
        
        // Добавление товаров в заказ
        foreach ($_SESSION['cart']['frames'] as $id => $item) {
            $update_query = "UPDATE Оправа SET Номер_заказа = ? WHERE Артикул = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ii", $order_id, $id);
            $stmt->execute();
        }
        
        // Очистка корзины
        $_SESSION['cart'] = [
            'frames' => [],
            'lenses' => []
        ];
        
        // Перенаправление на страницу успешного оформления заказа
        $_SESSION['order_success'] = true;
        $_SESSION['order_id'] = $order_id;
        header("Location: order_confirmation.php");
        exit;
    }
}

// Подсчет итоговой стоимости
$total_price = 0;
$total_items = 0;

// Оправы
if (!empty($_SESSION['cart']['frames'])) {
    $frame_ids = array_keys($_SESSION['cart']['frames']);
    $frame_ids_str = implode(',', array_map('intval', $frame_ids));
    
    $frames_query = "SELECT * FROM Оправа WHERE Артикул IN ($frame_ids_str)";
    $frames_result = $conn->query($frames_query);
    
    $frames_data = [];
    while ($row = $frames_result->fetch_assoc()) {
        $frames_data[$row['Артикул']] = $row;
        $quantity = $_SESSION['cart']['frames'][$row['Артикул']]['quantity'];
        $total_price += $row['Цена'] * $quantity;
        $total_items += $quantity;
    }
}

// Линзы
if (!empty($_SESSION['cart']['lenses'])) {
    $lens_ids = array_keys($_SESSION['cart']['lenses']);
    $lens_ids_str = implode(',', array_map('intval', $lens_ids));
    
    $lenses_query = "SELECT * FROM Линзы WHERE Артикул IN ($lens_ids_str)";
    $lenses_result = $conn->query($lenses_query);
    
    $lenses_data = [];
    while ($row = $lenses_result->fetch_assoc()) {
        $lenses_data[$row['Артикул']] = $row;
        $quantity = $_SESSION['cart']['lenses'][$row['Артикул']]['quantity'];
        $total_price += $row['Цена'] * $quantity;
        $total_items += $quantity;
    }
}
?>

<div class="cart-container">
    <h1 class="section-title">Корзина</h1>
    
    <?php if ($total_items === 0): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart fa-5x"></i>
            <p>Ваша корзина пуста</p>
            <a href="products.php" class="btn">Перейти к каталогу</a>
        </div>
    <?php else: ?>
        <form method="post" action="" class="cart-form">
            <div class="cart-items">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Товар</th>
                            <th>Цена</th>
                            <th>Количество</th>
                            <th>Сумма</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($_SESSION['cart']['frames'])): ?>
                            <?php foreach ($_SESSION['cart']['frames'] as $id => $item): ?>
                                <?php if (isset($frames_data[$id])): ?>
                                    <tr>
                                        <td class="product-info">
                                            <img src="images/frames/<?php echo $id; ?>.jpg" alt="<?php echo $frames_data[$id]['Название']; ?>" width="300" height="200">
                                            <div>
                                                <h3><?php echo $frames_data[$id]['Название']; ?></h3>
                                                <p>Производитель: <?php echo $frames_data[$id]['Производитель']; ?></p>
                                                <p>Материал: <?php echo $frames_data[$id]['Материал']; ?></p>
                                            </div>
                                        </td>
                                        <td class="product-price"><?php echo $frames_data[$id]['Цена']; ?> руб.</td>
                                        <td class="product-quantity">
                                            <input type="number" name="quantity[frame][<?php echo $id; ?>]" value="<?php echo $item['quantity']; ?>" min="1" max="10">
                                        </td>
                                        <td class="product-subtotal"><?php echo $frames_data[$id]['Цена'] * $item['quantity']; ?> руб.</td>
                                        <td class="product-remove">
                                            <a href="cart.php?remove=<?php echo $id; ?>&type=frame" class="remove-item"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <?php if (!empty($_SESSION['cart']['lenses'])): ?>
                            <?php foreach ($_SESSION['cart']['lenses'] as $id => $item): ?>
                                <?php if (isset($lenses_data[$id])): ?>
                                    <tr>
                                        <td class="product-info">
                                            <img src="images/lenses/<?php echo $id; ?>.jpg" alt="Линзы <?php echo $lenses_data[$id]['Производитель']; ?>" width="300" height="200">
                                            <div>
                                                <h3>Линзы <?php echo $lenses_data[$id]['Производитель']; ?></h3>
                                                <p>Тип: <?php echo $lenses_data[$id]['Тип']; ?></p>
                                                <p>Особенность: <?php echo $lenses_data[$id]['Особенность']; ?></p>
                                                <p>Диоптрии: <?php echo $lenses_data[$id]['Диоптрии']; ?></p>
                                            </div>
                                        </td>
                                        <td class="product-price"><?php echo $lenses_data[$id]['Цена']; ?> руб.</td>
                                        <td class="product-quantity">
                                            <input type="number" name="quantity[lens][<?php echo $id; ?>]" value="<?php echo $item['quantity']; ?>" min="1" max="10">
                                        </td>
                                        <td class="product-subtotal"><?php echo $lenses_data[$id]['Цена'] * $item['quantity']; ?> руб.</td>
                                        <td class="product-remove">
                                            <a href="cart.php?remove=<?php echo $id; ?>&type=lens" class="remove-item"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="cart-actions">
                <div class="update-cart">
                    <button type="submit" name="update_cart" class="btn">Обновить корзину</button>
                </div>
                <div class="cart-totals">
                    <table>
                        <tr>
                            <th>Итого:</th>
                            <td><?php echo $total_price; ?> руб.</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="checkout-section">
                <h2>Оформление заказа</h2>
                <div class="checkout-form">
                    <div class="form-group">
                        <label for="first_name">Имя *</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Фамилия *</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Телефон *</label>
                        <input type="tel" id="phone" name="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Адрес доставки *</label>
                        <textarea id="address" name="address" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="comment">Комментарий к заказу</label>
                        <textarea id="comment" name="comment" class="form-control"></textarea>
                    </div>
                    <button type="submit" name="checkout" class="btn btn-checkout">Оформить заказ</button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
