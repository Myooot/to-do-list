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
        <title><?= e($title) ?> | To-Do List</title>
        <link rel="stylesheet" href="<?= e(base_url('style.css')) ?>">
        <?php if ($pageCss !== null): ?>
            <link rel="stylesheet" href="<?= e($pageCss) ?>">
        <?php endif; ?>
    </head>

    <body>
        <header class="site-header">
            <div class="site-header__inner">
                <a class="brand" href="<?= e(base_url('pages/tasks/')) ?>">To-Do List</a>
                <nav class="nav" aria-label="Main navigation">
                    <a href="<?= e(base_url('pages/tasks/')) ?>">Tasks</a>
                    <a href="<?= e(base_url('pages/create/')) ?>">Add Task</a>
                </nav>
            </div>
        </header>

        <main class="page">
            <?php if ($flash !== null): ?>
                <div class="notice"><?= e($flash) ?></div>
            <?php endif; ?>
            <?= $content ?>
        </main>

        <footer class="site-footer">
            To-Do List by Corpuz & Remorosa.
        </footer>
    </body>

    </html>
<?php
}
