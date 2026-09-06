<?php
require_once __DIR__ . '/includes/db.php';

$error = null;
$login = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    $sql = "SELECT * FROM users WHERE login = '$login' AND password = '$password'";
    try {
        $row = db()->query($sql)->fetch();
        if ($row) {
            $_SESSION['user_id'] = $row['id'];
            header('Location: cabinet.php');
            exit;
        }
        $error = 'Невірний логін або пароль.';
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$pageTitle = 'Вхід';
require __DIR__ . '/includes/header.php';
?>
<section class="card shadow-sm">
    <div class="card-body p-4">
        <h1 class="h3 brand">Вхід до кабінету</h1>
        <?php if (!empty($_GET['registered'])): ?>
            <div class="alert alert-success">Реєстрація успішна. Увійдіть своїм логіном і паролем.</div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <div class="font-monospace small"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
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
                <button class="btn btn-gold px-4" type="submit">Увійти</button>
            </div>
        </form>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
