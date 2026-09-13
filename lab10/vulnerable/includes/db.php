<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('VULNSESSID');
    session_start();
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data';
    if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
        throw new RuntimeException('Не вдалося створити каталог data/');
    }

    $pdo = new PDO('sqlite:' . $dir . DIRECTORY_SEPARATOR . 'app.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    init_schema($pdo);
    return $pdo;
}

function init_schema(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            login TEXT NOT NULL,
            password TEXT NOT NULL,
            phone TEXT,
            created_at TEXT
        )'
    );
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS news (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            body TEXT NOT NULL
        )'
    );

    $users = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($users === 0) {
        $pdo->exec(
            "INSERT INTO users (login, password, phone, created_at) VALUES
            ('admin', 'admin', '+380501112233', datetime('now')),
            ('olena', '12345', '+380671234567', datetime('now')),
            ('taras', 'taras2024', '+380931112233', datetime('now')),
            ('student', 'student', '+380441112233', datetime('now'))"
        );
    }

    $news = (int) $pdo->query('SELECT COUNT(*) FROM news')->fetchColumn();
    if ($news === 0) {
        $pdo->exec(
            "INSERT INTO news (title, body) VALUES
            (
                'Старт кампусної лотереї «Щасливий квиток»',
                'Реєструйтеся на сайті, зберігайте свій логін і стежте за новинами розіграшу. Переможців визначаємо серед зареєстрованих учасників кампусу.'
            ),
            (
                'Як перевірити свій квиток',
                'Після входу в кабінет ви побачите свій номер телефону, який бере участь у розіграші. Новини кожного тижня публікуємо в розділі «Новини».'
            ),
            (
                'Перший тижневий розіграш',
                'Цього тижня розігруємо канцтовари й безкоштовну каву в кампусному кафе. Результати з''являться в окремій новині після підбиття підсумків.'
            ),
            (
                'Правила участі',
                'Один логін — один учасник. Номер телефону потрібен, щоб ми могли повідомити про виграш. Адміністрація не просить пароль у месенджерах.'
            ),
            (
                'Підсумки пробного тиражу',
                'Пробний тираж завершено. Дякуємо всім, хто зареєструвався. Наступний розіграш анонсуємо в цій стрічці новин.'
            )"
        );
    }
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $id = (int) $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE id = $id";
    $row = db()->query($sql)->fetch();
    return $row ?: null;
}
