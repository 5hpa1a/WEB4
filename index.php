<?php include 'header.php'; ?>

<section class="hero">
    <div class="hero-content">
        <h2>Добро пожаловать в ОптикаПро</h2>
        <p>Мы предлагаем широкий выбор оправ и линз от ведущих производителей</p>
        <a href="products.php" class="btn">Перейти к каталогу</a>
    </div>
    <div class="hero-image">
        <img src="images/hero-glasses.jpg" alt="Очки" width="300" height="200">
    </div>
</section>

<section class="features">
    <h2 class="section-title">Почему выбирают нас?</h2>
    <div class="features-container">
        <div class="feature-card">
            <i class="fas fa-glasses"></i>
            <h3>Широкий ассортимент</h3>
            <p>Более 1000 моделей оправ и линз от ведущих мировых брендов</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-user-md"></i>
            <h3>Профессиональные консультации</h3>
            <p>Наши специалисты подберут идеальные очки под ваши потребности</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-tools"></i>
            <h3>Сервисное обслуживание</h3>
            <p>Бесплатный ремонт и настройка очков в течение гарантийного срока</p>
        </div>
    </div>
</section>

<section class="popular-products">
    <h2 class="section-title">Популярные модели</h2>
    <div class="products-grid">
        <?php
        $query = "SELECT Оправа.Артикул, Оправа.Название, Оправа.Производитель, Оправа.Цена 
                  FROM Оправа 
                  ORDER BY Оправа.Цена DESC 
                  LIMIT 4";
        $result = $conn->query($query);
        
        while ($row = $result->fetch_assoc()) {
            echo '<div class="product-card">';
            echo '<img src="images/frames/' . $row['Артикул'] . '.jpg" alt="' . $row['Название'] . '" width="300" height="200">';
            echo '<h3>' . $row['Название'] . '</h3>';
            echo '<p class="product-brand">' . $row['Производитель'] . '</p>';
            echo '<p class="product-price">' . $row['Цена'] . ' руб.</p>';
            echo '<a href="product_details.php?id=' . $row['Артикул'] . '" class="btn">Подробнее</a>';
            echo '</div>';
        }
        ?>
    </div>
    <div class="center">
        <a href="products.php" class="btn btn-outline">Смотреть все товары</a>
    </div>
</section>

<section class="testimonials">
    <h2 class="section-title">Отзывы наших клиентов</h2>
    <div class="testimonials-container">
        <div class="testimonial">
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
            </div>
            <p>Прекрасный магазин с большим выбором оправ. Консультант помог подобрать идеальные очки для моей формы лица. Очень довольна!</p>
            <div class="testimonial-author">Анна С.</div>
        </div>
        <div class="testimonial">
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
            </div>
            <p>Заказывал очки с прогрессивными линзами. Качество отличное, а цена значительно ниже, чем в других салонах.</p>
            <div class="testimonial-author">Петр В.</div>
        </div>
    </div>
</section>

<section class="news">
    <h2 class="section-title">Новости и акции</h2>
    <div class="news-container">
        <div class="news-item">
            <div class="news-date">15 марта 2025</div>
            <h3>Новая коллекция оправ Ray-Ban</h3>
            <p>В нашем магазине появилась новая весенняя коллекция оправ от Ray-Ban. Стильные модели для мужчин и женщин.</p>
            <a href="#" class="read-more">Подробнее</a>
        </div>
        <div class="news-item">
            <div class="news-date">10 марта 2025</div>
            <h3>Скидка 20% на все солнцезащитные очки</h3>
            <p>Готовимся к лету! Весь март действует скидка 20% на все солнцезащитные очки.</p>
            <a href="#" class="read-more">Подробнее</a>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
