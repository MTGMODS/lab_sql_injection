<?php
require_once __DIR__ . '/includes/db.php';

$error = null;
$login = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $sql = "INSERT INTO users (login, password, phone, created_at) VALUES ('$login', '$password', '$phone', datetime('now'))";
    try {
        db()->exec($sql);
        header('Location: login.php?registered=1');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$pageTitle = 'Реєстрація';
require __DIR__ . '/includes/header.php';
$phone = $phone ?? '';
?>
<section class="card shadow-sm">
    <div class="card-body p-4">
        <h1 class="h3 brand">Реєстрація учасника</h1>
        <p class="text-muted">Логін і пароль записуються в таблицю <code>users</code> як є, без хешування.</p>
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <strong>Помилка SQL:</strong>
                <div class="font-monospace small mt-2"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        <?php endif; ?>
        <form method="post" class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="login">Логін</label>
                <input class="form-control" id="login" name="login" required
                       value="<?= htmlspecialchars($login, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="password">Пароль</label>
                <input class="form-control" id="password" name="password" type="password" required>
            </div>
            <div class="col-12">
                <label class="form-label" for="phone">Номер телефону</label>
                <input class="form-control" id="phone" name="phone" placeholder="+380..."
                       value="<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-12">
                <button class="btn btn-gold px-4" type="submit">Зареєструватися</button>
            </div>
        </form>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
