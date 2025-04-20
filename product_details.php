<?php
include 'header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'frame';

if ($id <= 0) {
    header("Location: products.php");
    exit;
}

if ($type === 'frame') {
    $query = "SELECT * FROM Оправа WHERE Артикул = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header("Location: products.php");
        exit;
    }
    
    $product = $result->fetch_assoc();
} elseif ($type === 'lens') {
    $query = "SELECT * FROM Линзы WHERE Артикул = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header("Location: products.php");
        exit;
    }
    
    $product = $result->fetch_assoc();
} else {
    header("Location: products.php");
    exit;
}
?>

<div class="product-details-container">
    <div class="breadcrumbs">
        <a href="index.php">Главная</a> &gt; 
        <a href="products.php">Каталог</a> &gt; 
        <?php if ($type === 'frame'): ?>
            <a href="products.php?category=frames">Оправы</a> &gt; 
            <?php echo $product['Название']; ?>
        <?php elseif ($type === 'lens'): ?>
            <a href="products.php?category=lenses">Линзы</a> &gt; 
            Линзы <?php echo $product['Производитель']; ?>
        <?php endif; ?>
    </div>
    
    <div class="product-details">
        <div class="product-gallery">
            <div class="product-main-image">
                <img src="images/<?php echo $type === 'frame' ? 'frames' : 'lenses'; ?>/<?php echo $id; ?>.jpg" alt="<?php echo $type === 'frame' ? $product['Название'] : 'Линзы ' . $product['Производитель']; ?>" width="300" height="200">
            </div>
            <div class="product-thumbnails">
                <div class="thumbnail active">
                    <img src="images/<?php echo $type === 'frame' ? 'frames' : 'lenses'; ?>/<?php echo $id; ?>.jpg" alt="Фото 1" width="300" height="200">
                </div>
                <!-- Дополнительные фото товара, если есть -->
            </div>
        </div>
        
        <div class="product-info">
            <?php if ($type === 'frame'): ?>
                <h1 class="product-title"><?php echo $product['Название']; ?></h1>
                <div class="product-meta">
                    <p class="product-brand">Производитель: <?php echo $product['Производитель']; ?></p>
                    <p class="product-material">Материал: <?php echo $product['Материал']; ?></p>
                </div>
            <?php elseif ($type === 'lens'): ?>
                <h1 class="product-title">Линзы <?php echo $product['Производитель']; ?></h1>
                <div class="product-meta">
                    <p class="product-type">Тип: <?php echo $product['Тип']; ?></p>
                    <p class="product-feature">Особенность: <?php echo $product['Особенность']; ?></p>
                    <p class="product-dioptry">Диоптрии: <?php echo $product['Диоптрии']; ?></p>
                </div>
            <?php endif; ?>
            
            <div class="product-price">
                <span class="price"><?php echo $product['Цена']; ?> руб.</span>
            </div>
            
            <div class="product-description">
                <h3>Описание</h3>
                <p>
                    <?php if ($type === 'frame'): ?>
                        Стильная оправа <?php echo $product['Название']; ?> от производителя <?php echo $product['Производитель']; ?> изготовлена из качественного материала <?php echo $product['Материал']; ?>. Подходит для повседневного использования и отлично сочетается с различными стилями одежды.
                    <?php elseif ($type === 'lens'): ?>
                        Линзы <?php echo $product['Производитель']; ?> типа "<?php echo $product['Тип']; ?>" с диоптриями <?php echo $product['Диоптрии']; ?>. Особенность: <?php echo $product['Особенность']; ?>. Обеспечивают отличное качество зрения и комфорт при ношении.
                    <?php endif; ?>
                </p>
            </div>
            
            <div class="product-actions">
                <div class="quantity-selector">
                    <button class="quantity-btn minus">-</button>
                    <input type="number" id="product-quantity" value="1" min="1" max="10">
                    <button class="quantity-btn plus">+</button>
                </div>
                <button class="btn btn-add-to-cart" data-id="<?php echo $id; ?>" data-type="<?php echo $type; ?>">Добавить в корзину</button>
            </div>
        </div>
    </div>
    
    <div class="product-tabs">
        <div class="tabs-nav">
            <button class="tab-btn active" data-tab="tab-details">Характеристики</button>
            <button class="tab-btn" data-tab="tab-delivery">Доставка и оплата</button>
            <button class="tab-btn" data-tab="tab-reviews">Отзывы</button>
        </div>
        
        <div class="tabs-content">
            <div id="tab-details" class="tab-pane active">
                <h3>Характеристики</h3>
                <table class="product-specs">
                    <?php if ($type === 'frame'): ?>
                        <tr>
                            <th>Производитель</th>
                            <td><?php echo $product['Производитель']; ?></td>
                        </tr>
                        <tr>
                            <th>Название модели</th>
                            <td><?php echo $product['Название']; ?></td>
                        </tr>
                        <tr>
                            <th>Материал</th>
                            <td><?php echo $product['Материал']; ?></td>
                        </tr>
                        <tr>
                            <th>Артикул</th>
                            <td><?php echo $product['Артикул']; ?></td>
                        </tr>
                    <?php elseif ($type === 'lens'): ?>
                        <tr>
                            <th>Производитель</th>
                            <td><?php echo $product['Производитель']; ?></td>
                        </tr>
                        <tr>
                            <th>Тип</th>
                            <td><?php echo $product['Тип']; ?></td>
                        </tr>
                        <tr>
                            <th>Особенность</th>
                            <td><?php echo $product['Особенность']; ?></td>
                        </tr>
                        <tr>
                            <th>Диоптрии</th>
                            <td><?php echo $product['Диоптрии']; ?></td>
                        </tr>
                        <tr>
                            <th>Артикул</th>
                            <td><?php echo $product['Артикул']; ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
            
            <div id="tab-delivery" class="tab-pane">
                <h3>Доставка и оплата</h3>
                <p>Мы предлагаем различные способы доставки и оплаты для вашего удобства:</p>
                <h4>Способы доставки:</h4>
                <ul>
                    <li>Самовывоз из магазина (бесплатно)</li>
                    <li>Курьерская доставка по городу (300 руб.)</li>
                    <li>Доставка по России (от 400 руб., в зависимости от региона)</li>
                </ul>
                <h4>Способы оплаты:</h4>
                <ul>
                    <li>Наличными при получении</li>
                    <li>Банковской картой при получении</li>
                    <li>Онлайн-оплата картой через сайт</li>
                    <li>Банковский перевод</li>
                </ul>
            </div>
            
            <div id="tab-reviews" class="tab-pane">
                <h3>Отзывы</h3>
                <!-- Здесь можно вывести отзывы о товаре, если они есть в БД -->
                <p>Отзывы о данном товаре отсутствуют.</p>
                <a href="guestbook.php" class="btn">Оставить отзыв</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Переключение вкладок
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Удаляем класс active у всех кнопок и вкладок
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Добавляем класс active активной кнопке и вкладке
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Изменение количества товара
    const quantityInput = document.getElementById('product-quantity');
    const minusBtn = document.querySelector('.quantity-btn.minus');
    const plusBtn = document.querySelector('.quantity-btn.plus');
    
    minusBtn.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        if (value > 1) {
            quantityInput.value = value - 1;
        }
    });
    
    plusBtn.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        if (value < 10) {
            quantityInput.value = value + 1;
        }
    });
    
    // Добавление в корзину
    const addToCartBtn = document.querySelector('.btn-add-to-cart');
    
    addToCartBtn.addEventListener('click', function() {
        const productId = this.getAttribute('data-id');
        const productType = this.getAttribute('data-type');
        const quantity = parseInt(quantityInput.value);
        
        // AJAX запрос для добавления товара в корзину
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&product_type=${productType}&quantity=${quantity}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Товар добавлен в корзину!');
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
    });
});
</script>

<?php include 'footer.php'; ?>
