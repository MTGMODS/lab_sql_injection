<?php
$pageTitle = 'Головна';
require __DIR__ . '/includes/header.php';

$news = db()->query('SELECT id, title FROM news ORDER BY id')->fetchAll();
$participants = (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
?>
<section class="hero p-4 p-md-5 mb-4">
    <p class="text-uppercase small fw-bold mb-2" style="letter-spacing:.14em;color:#0f6b45;">Кампусна лотерея</p>
    <h1 class="brand display-5">Той самий сайт, але без SQL-ін’єкції</h1>
    <p class="mb-4">Реєстрація, вхід і новини працюють як раніше. Відмінність одна: дані користувача більше не склеюються в текст SQL-запиту.</p>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-gold px-4" href="register.php">Реєстрація</a>
        <a class="btn btn-outline-dark" href="login.php">Вхід до кабінету</a>
        <a class="btn btn-outline-success" href="recommendations.php">Рекомендації розробникам</a>
    </div>
    <p class="mt-4 mb-0 small text-muted">Зараз у розіграші: <?= $participants ?> учасників</p>
</section>

<section class="card shadow-sm">
    <div class="card-body">
        <h2 class="h4 brand">Новини розіграшу</h2>
        <p class="text-muted">Адреса лишилась тією ж: <code>news.php?id=</code>, але <code>id</code> передається як параметр запиту.</p>
        <ul class="list-group list-group-flush">
            <?php foreach ($news as $item): ?>
                <li class="list-group-item px-0">
                    <a href="news.php?id=<?= (int) $item['id'] ?>">
                        <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <div class="small text-muted">news.php?id=<?= (int) $item['id'] ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
