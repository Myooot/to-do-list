<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function render_page(string $title, string $content, ?string $pageCss = null): void
{
    $flash = flash_message();
?>
    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#4f46e5">
        <title><?= e($title) ?> | To-Do List</title>
        <link rel="stylesheet" href="<?= e(base_url('style.css')) ?>">
        <?php if ($pageCss !== null): ?>
            <link rel="stylesheet" href="<?= e($pageCss) ?>">
        <?php endif; ?>
    </head>

    <body>
        <header class="site-header">
            <div class="site-header__inner">
                <a class="brand" href="<?= e(base_url('pages/tasks/')) ?>" aria-label="To-Do List home">
                    <span class="brand__mark" aria-hidden="true">✓</span>
                    <span>To-Do List</span>
                </a>
                <nav class="nav" aria-label="Main navigation">
                    <a href="<?= e(base_url('pages/tasks/')) ?>">Tasks</a>
                    <a class="nav__primary" href="<?= e(base_url('pages/create/')) ?>"><span aria-hidden="true">+</span> Add Task</a>
                </nav>
            </div>
        </header>

        <main class="page">
            <?php if ($flash !== null): ?>
                <div class="notice" role="status" aria-live="polite">
                    <span class="notice__icon" aria-hidden="true">✓</span>
                    <span><?= e($flash) ?></span>
                    <button class="notice__close" type="button" aria-label="Dismiss notification">×</button>
                </div>
            <?php endif; ?>
            <?= $content ?>
        </main>

        <footer class="site-footer">
            To-Do List by Corpuz & Remorosa.
        </footer>
        <script src="<?= e(base_url('app.js')) ?>" defer></script>
    </body>

    </html>
<?php
}
