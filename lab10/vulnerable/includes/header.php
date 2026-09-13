<?php
require_once __DIR__ . '/db.php';
$pageTitle = $pageTitle ?? 'Щасливий квиток';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> · Щасливий квиток</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #14213d;
            --gold: #d4a017;
            --paper: #f6efe3;
        }
        body {
            font-family: Manrope, system-ui, sans-serif;
            background: radial-gradient(circle at top right, #2a3a66 0, var(--ink) 42%, #0b1020 100%);
            min-height: 100vh;
            color: #1b1b1b;
        }
        .ticket-wrap { max-width: 980px; }
        .brand {
            font-family: Fraunces, Georgia, serif;
            letter-spacing: .02em;
        }
        .badge-lab {
            background: #8b1e1e;
            color: #fff;
            font-weight: 700;
            letter-spacing: .08em;
        }
        .navbar, .card, .hero {
            background: var(--paper);
        }
        .navbar { border-bottom: 3px dashed var(--gold); }
        .hero {
            border: 1px solid #e2d3b3;
            border-radius: 18px;
        }
        .ticket-num {
            font-family: Fraunces, Georgia, serif;
            color: var(--gold);
        }
        a { color: #7a4e00; }
        .btn-gold {
            background: var(--gold);
            border: 0;
            color: #1b1404;
            font-weight: 700;
        }
        .btn-gold:hover { background: #b8860b; color: #1b1404; }
        footer { color: #d7d0c4; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg mb-4">
    <div class="container ticket-wrap">
        <a class="navbar-brand brand d-flex align-items-center gap-2" href="index.php">
            <span class="ticket-num">№10</span>
            <span>Щасливий квиток</span>
        </a>
        <span class="badge badge-lab me-3">ВРАЗЛИВА ВЕРСІЯ</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Головна</a></li>
                <li class="nav-item"><a class="nav-link" href="news.php?id=1">Новини</a></li>
                <?php if ($user): ?>
                    <li class="nav-item"><a class="nav-link" href="cabinet.php">Кабінет</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Вихід</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="register.php">Реєстрація</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Вхід</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container ticket-wrap pb-5">
