<?php
session_start();
include 'db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Необходимо авторизоваться']);
    exit;
}

// Инициализация корзины, если она не существует
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [
        'frames' => [],
        'lenses' => []
    ];
}

// Получение данных
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$product_type = isset($_POST['product_type']) ? $_POST['product_type'] : '';
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($product_id <= 0 || empty($product_type) || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Некорректные параметры']);
    exit;
}

// Добавление товара в корзину
if ($product_type === 'frame') {
    // Проверка существования товара
    $query = "SELECT * FROM Оправа WHERE Артикул = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Товар существует, добавляем в корзину
        if (isset($_SESSION['cart']['frames'][$product_id])) {
            // Если товар уже в корзине, увеличиваем количество
            $_SESSION['cart']['frames'][$product_id]['quantity'] += $quantity;
        } else {
            // Добавляем новый товар
            $_SESSION['cart']['frames'][$product_id] = [
                'quantity' => $quantity
            ];
        }
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Товар не найден']);
    }
} elseif ($product_type === 'lens') {
    // Проверка существования товара
    $query = "SELECT * FROM Линзы WHERE Артикул = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Товар существует, добавляем в корзину
        if (isset($_SESSION['cart']['lenses'][$product_id])) {
            // Если товар уже в корзине, увеличиваем количество
            $_SESSION['cart']['lenses'][$product_id]['quantity'] += $quantity;
        } else {
            // Добавляем новый товар
            $_SESSION['cart']['lenses'][$product_id] = [
                'quantity' => $quantity
            ];
        }
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Товар не найден']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Неизвестный тип товара']);
}
?>
