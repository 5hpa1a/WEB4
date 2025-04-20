<?php include 'header.php'; ?>

<h1 class="section-title">Галерея нашей продукции</h1>

<div class="gallery-filters">
    <button class="filter-btn active" data-filter="all">Все</button>
    <button class="filter-btn" data-filter="frames">Оправы</button>
    <button class="filter-btn" data-filter="sunglasses">Солнцезащитные очки</button>
    <button class="filter-btn" data-filter="lenses">Линзы</button>
</div>

<div class="gallery-container">
    <?php
    // Получение данных оправ из БД
    $query = "SELECT Артикул, Название, Производитель, Материал FROM Оправа LIMIT 15";
    $result = $conn->query($query);
    
    while ($row = $result->fetch_assoc()) {
        echo '<div class="gallery-item" data-category="frames">';
        echo '<img src="images/frames/' . $row['Артикул'] . '.jpg" alt="' . $row['Название'] . '" width="300" height="200">';
        echo '<div class="gallery-item-info">';
        echo '<h3>' . $row['Название'] . '</h3>';
        echo '<p>Производитель: ' . $row['Производитель'] . '</p>';
        echo '<p>Материал: ' . $row['Материал'] . '</p>';
        echo '<a href="product_details.php?id=' . $row['Артикул'] . '" class="btn">Подробнее</a>';
        echo '</div>';
        echo '</div>';
    }
    
    // Получение данных линз из БД
    $query = "SELECT Артикул, Производитель, Тип, Особенность FROM Линзы LIMIT 10";
    $result = $conn->query($query);
    
    while ($row = $result->fetch_assoc()) {
        echo '<div class="gallery-item" data-category="lenses">';
        echo '<img src="images/lenses/' . $row['Артикул'] . '.jpg" alt="Линзы ' . $row['Производитель'] . '" width="300" height="200">';
        echo '<div class="gallery-item-info">';
        echo '<h3>Линзы ' . $row['Производитель'] . '</h3>';
        echo '<p>Тип: ' . $row['Тип'] . '</p>';
        echo '<p>Особенность: ' . $row['Особенность'] . '</p>';
        echo '<a href="product_details.php?type=lens&id=' . $row['Артикул'] . '" class="btn">Подробнее</a>';
        echo '</div>';
        echo '</div>';
    }
    ?>
    
    <!-- Для солнцезащитных очков можно добавить фиктивные данные или создать отдельную таблицу в БД -->
    <div class="gallery-item" data-category="sunglasses">
        <img src="images/frames/6.jpg" alt="Солнцезащитные очки" width="300" height="200">
        <div class="gallery-item-info">
            <h3>Ray-Ban Wayfarer</h3>
            <p>Производитель: Ray-Ban</p>
            <p>Материал: Ацетат</p>
            <a href="#" class="btn">Подробнее</a>
        </div>
    </div>
    <!-- Еще солнцезащитные очки... -->
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Удаляем класс active у всех кнопок
            filterBtns.forEach(b => b.classList.remove('active'));
            // Добавляем класс active текущей кнопке
            btn.classList.add('active');
            
            const filter = btn.getAttribute('data-filter');
            
            galleryItems.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php include 'footer.php'; ?>
