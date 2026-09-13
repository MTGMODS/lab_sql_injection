<?php
require_once __DIR__ . '/includes/db.php';

$error = null;
$login = '';
$phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim((string) ($_POST['login'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $phone = trim((string) ($_POST['phone'] ?? ''));

    if ($login === '' || $password === '') {
        $error = 'Вкажіть логін і пароль.';
    } elseif (strlen($login) > 32 || strlen($password) > 64) {
        $error = 'Занадто довгий логін або пароль.';
    } else {
        try {
            $check = db()->prepare('SELECT id FROM users WHERE login = :login');
            $check->execute(['login' => $login]);
            if ($check->fetch()) {
                $error = 'Такий логін уже зайнятий.';
            } else {
                $stmt = db()->prepare(
                    'INSERT INTO users (login, password, phone, created_at)
                     VALUES (:login, :password, :phone, datetime(\'now\'))'
                );
                $stmt->execute([
                    'login' => $login,
                    'password' => $password,
                    'phone' => $phone,
                ]);
                header('Location: login.php?registered=1');
                exit;
            }
        } catch (Throwable $e) {
            $error = 'Не вдалося завершити реєстрацію.';
        }
    }
}

$pageTitle = 'Реєстрація';
require __DIR__ . '/includes/header.php';
?>
<section class="card shadow-sm">
    <div class="card-body p-4">
        <h1 class="h3 brand">Реєстрація учасника</h1>
        <p class="text-muted">Дані пишуться через <code>PDO::prepare()</code>. Рядок SQL не змінюється від введення користувача.</p>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <form method="post" class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="login">Логін</label>
                <input class="form-control" id="login" name="login" required maxlength="32"
                       value="<?= htmlspecialchars($login, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="password">Пароль</label>
                <input class="form-control" id="password" name="password" type="password" required maxlength="64">
            </div>
            <div class="col-12">
                <label class="form-label" for="phone">Номер телефону</label>
                <input class="form-control" id="phone" name="phone" placeholder="+380..." maxlength="32"
                       value="<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-12">
                <button class="btn btn-gold px-4" type="submit">Зареєструватися</button>
            </div>
        </form>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
