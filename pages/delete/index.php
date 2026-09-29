<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/layout.php';

$id = (string)($_GET['id'] ?? '');
$task = $id !== '' ? find_task($id) : null;

if ($task === null) {
    set_flash('Task not found.');
    redirect_to('pages/tasks/');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_token_is_valid($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Your session expired. Please try again.';
    }

    if ($errors === []) {
        $tasks = array_filter(read_tasks(), fn(array $item): bool => ($item['id'] ?? '') !== $id);

        save_tasks($tasks);
        set_flash('Task deleted successfully.');
        redirect_to('pages/tasks/');
    }
}

ob_start();
?>
<section class="page-title">
    <div>
        <h1>Delete Task</h1>
        <p>Please confirm before removing this task.</p>
    </div>
</section>

<section class="panel confirm-box">
    <h2><?= e($task['title']) ?></h2>
    <p>This action cannot be undone.</p>
    <form method="post">
        <?= csrf_field() ?>

        <?php if ($errors !== []): ?>
            <ul class="error-list">
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="actions">
            <button class="button button--danger" type="submit">Delete Task</button>
            <a class="button button--quiet" href="<?= e(base_url('pages/view/?id=' . urlencode($id))) ?>">Cancel</a>
        </div>
    </form>
</section>
<?php

render_page('Delete Task', ob_get_clean(), base_url('pages/delete/style.css'));
