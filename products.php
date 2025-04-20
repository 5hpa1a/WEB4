<?php include 'header.php'; ?>

<div class="products-container">
    <h1 class="section-title">Каталог продукции</h1>
    
    <div class="products-grid-layout">
        <aside class="filters-sidebar">
            <h3>Фильтры</h3>
            <form method="get" action="" id="filter-form">
                <div class="filter-section">
                    <h4>Категория</h4>
                    <div class="filter-options">
                        <label>
                            <input type="radio" name="category" value="frames" <?php echo (!isset($_GET['category']) || $_GET['category'] == 'frames') ? 'checked' : ''; ?>>
                            Оправы
                        </label>
                        <label>
                            <input type="radio" name="category" value="lenses" <?php echo (isset($_GET['category']) && $_GET['category'] == 'lenses') ? 'checked' : ''; ?>>
                            Линзы
                        </label>
                    </div>
                </div>
                
                <div class="filter-section">
                    <h4>Производитель</h4>
                    <div class="filter-options">
                        <?php
                        // Получение списка производителей из БД
                        $query = "SELECT DISTINCT Производитель FROM Оправа ORDER BY Производитель";
                        $result = $conn->query($query);
                        
                        $manufacturers = [];
                        while ($row = $result->fetch_assoc()) {
                            $manufacturers[] = $row['Производитель'];
                        }
                        
                        foreach ($manufacturers as $manufacturer) {
                            echo '<label>';
                            echo '<input type="checkbox" name="manufacturer[]" value="' . $manufacturer . '"';
                            if (isset($_GET['manufacturer']) && in_array($manufacturer, $_GET['manufacturer'])) {
                                echo ' checked';
                            }
                            echo '> ' . $manufacturer;
                            echo '</label>';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="filter-section">
                    <h4>Материал</h4>
                    <div class="filter-options">
                        <?php
                        // Получение списка материалов из БД
                        $query = "SELECT DISTINCT Материал FROM Оправа ORDER BY Материал";
                        $result = $conn->query($query);
                        
                        $materials = [];
                        while ($row = $result->fetch_assoc()) {
                            $materials[] = $row['Материал'];
                        }
                        
                        foreach ($materials as $material) {
                            echo '<label>';
                            echo '<input type="checkbox" name="material[]" value="' . $material . '"';
                            if (isset($_GET['material']) && in_array($material, $_GET['material'])) {
                                echo ' checked';
                            }
                            echo '> ' . $material;
                            echo '</label>';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="filter-section">
                    <h4>Цена</h4>
                    <div class="price-range">
                        <input type="number" name="min_price" placeholder="От" value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>">
                        <span>—</span>
                        <input type="number" name="max_price" placeholder="До" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>">
                    </div>
                </div>
                
                <div class="filter-buttons">
                    <button type="submit" class="btn">Применить</button>
                    <a href="products.php" class="btn btn-reset">Сбросить</a>
                </div>
            </form>
        </aside>
        
        <div class="products-grid">
            <?php
            // Формирование запроса с учетом фильтров
            $category = isset($_GET['category']) ? $_GET['category'] : 'frames';
            
            if ($category === 'frames') {
                $query = "SELECT * FROM Оправа WHERE 1=1";
                
                // Фильтр по производителю
                if (isset($_GET['manufacturer']) && !empty($_GET['manufacturer'])) {
                    $manufacturerFilter = implode("','", $_GET['manufacturer']);
                    $query .= " AND Производитель IN ('$manufacturerFilter')";
                }
                
                // Фильтр по материалу
                if (isset($_GET['material']) && !empty($_GET['material'])) {
                    $materialFilter = implode("','", $_GET['material']);
                    $query .= " AND Материал IN ('$materialFilter')";
                }
                
                // Фильтр по цене
                if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
                    $minPrice = (int)$_GET['min_price'];
                    $query .= " AND Цена >= $minPrice";
                }
                
                if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
                    $maxPrice = (int)$_GET['max_price'];
                    $query .= " AND Цена <= $maxPrice";
                }
                
                $query .= " ORDER BY Цена ASC";
                $result = $conn->query($query);
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="product-card">';
                        echo '<img src="images/frames/' . $row['Артикул'] . '.jpg" alt="' . $row['Название'] . '" width="300" height="200">';
                        echo '<h3>' . $row['Название'] . '</h3>';
                        echo '<p class="product-brand">' . $row['Производитель'] . '</p>';
                        echo '<p class="product-material">Материал: ' . $row['Материал'] . '</p>';
                        echo '<p class="product-price">' . $row['Цена'] . ' руб.</p>';
                        echo '<div class="product-actions">';
                        echo '<a href="product_details.php?id=' . $row['Артикул'] . '" class="btn btn-details">Подробнее</a>';
                        echo '<button class="btn btn-cart" data-id="' . $row['Артикул'] . '" data-type="frame">В корзину</button>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="no-products">Товары не найдены.</p>';
                }
            } elseif ($category === 'lenses') {
                $query = "SELECT * FROM Линзы WHERE 1=1";
                
                // Фильтр по производителю
                if (isset($_GET['manufacturer']) && !empty($_GET['manufacturer'])) {
                    $manufacturerFilter = implode("','", $_GET['manufacturer']);
                    $query .= " AND Производитель IN ('$manufacturerFilter')";
                }
                
                // Фильтр по цене
                if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
                    $minPrice = (int)$_GET['min_price'];
                    $query .= " AND Цена >= $minPrice";
                }
                
                if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
                    $maxPrice = (int)$_GET['max_price'];
                    $query .= " AND Цена <= $maxPrice";
                }
                
                $query .= " ORDER BY Цена ASC";
                $result = $conn->query($query);
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="product-card">';
                        echo '<img src="images/lenses/' . $row['Артикул'] . '.jpg" alt="Линзы ' . $row['Производитель'] . '" width="300" height="200">';
                        echo '<h3>Линзы ' . $row['Производитель'] . '</h3>';
                        echo '<p class="product-type">Тип: ' . $row['Тип'] . '</p>';
                        echo '<p class="product-feature">Особенность: ' . $row['Особенность'] . '</p>';
                        echo '<p class="product-dioptry">Диоптрии: ' . $row['Диоптрии'] . '</p>';
                        echo '<p class="product-price">' . $row['Цена'] . ' руб.</p>';
                        echo '<div class="product-actions">';
                        echo '<a href="product_details.php?type=lens&id=' . $row['Артикул'] . '" class="btn btn-details">Подробнее</a>';
                        echo '<button class="btn btn-cart" data-id="' . $row['Артикул'] . '" data-type="lens">В корзину</button>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="no-products">Товары не найдены.</p>';
                }
            }
            ?>
        </div>
    </div>
</div>

<script>
// JavaScript для добавления товаров в корзину
document.addEventListener('DOMContentLoaded', function() {
    const cartButtons = document.querySelectorAll('.btn-cart');
    
    cartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const productType = this.getAttribute('data-type');
            
            // AJAX запрос для добавления товара в корзину
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&product_type=${productType}&quantity=1`
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
});
</script>

<?php include 'footer.php'; ?>
