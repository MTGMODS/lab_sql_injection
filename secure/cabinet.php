<?php
require __DIR__ . '/includes/db.php';
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Кабінет';
require __DIR__ . '/includes/header.php';
$user = current_user();
if (!$user) {
    header('Location: logout.php');
    exit;
}
?>
<section class="card shadow-sm">
    <div class="card-body p-4">
        <h1 class="h3 brand">Особистий кабінет</h1>
        <p>Вітаємо, <strong><?= htmlspecialchars($user['login'], ENT_QUOTES, 'UTF-8') ?></strong>.</p>
        <dl class="row mb-0">
            <dt class="col-sm-3">Логін</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($user['login'], ENT_QUOTES, 'UTF-8') ?></dd>
            <dt class="col-sm-3">Телефон</dt>
            <dd class="col-sm-9"><?= htmlspecialchars((string) $user['phone'], ENT_QUOTES, 'UTF-8') ?></dd>
        </dl>
        <a class="btn btn-outline-dark mt-4" href="logout.php">Вийти</a>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
