<?php
$pageTitle = 'Новина';
require __DIR__ . '/includes/header.php';

$id = $_GET['id'] ?? '';
$error = null;
$row = null;

if ($id === '' || !ctype_digit((string) $id)) {
    $error = 'Некоректний ідентифікатор новини. Очікується додатне число.';
} else {
    try {
        $stmt = db()->prepare('SELECT id, title, body FROM news WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
    } catch (Throwable $e) {
        $error = 'Не вдалося завантажити новину.';
    }
}
?>
<article class="card shadow-sm">
    <div class="card-body">
        <p class="small text-muted mb-2">Запит: <code>news.php?id=<?= htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') ?></code></p>
        <?php if ($error): ?>
            <div class="alert alert-warning"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php elseif (!$row): ?>
            <h1 class="h3 brand">Новину не знайдено</h1>
            <p>Немає запису з таким ідентифікатором.</p>
        <?php else: ?>
            <h1 class="h3 brand"><?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="mb-0"><?= htmlspecialchars($row['body'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <a class="btn btn-outline-dark mt-4" href="index.php">На головну</a>
    </div>
</article>
<?php require __DIR__ . '/includes/footer.php'; ?>
