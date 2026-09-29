<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle') {
    $id = (string)($_POST['id'] ?? '');
    $tasks = read_tasks();
    $newStatus = null;

    foreach ($tasks as &$item) {
        if (($item['id'] ?? '') === $id) {
            $newStatus = ($item['status'] ?? 'pending') === 'done' ? 'pending' : 'done';
            $item['status'] = $newStatus;
            $item['updated_at'] = date('Y-m-d H:i:s');
            break;
        }
    }
    unset($item);

    if ($newStatus !== null) {
        save_tasks($tasks);
        set_flash($newStatus === 'done' ? 'Task marked as complete.' : 'Task reopened.');
    } else {
        set_flash('Task not found.');
    }

    redirect_to('pages/tasks/');
}

$tasks = read_tasks();
$completedCount = count(array_filter($tasks, fn(array $task): bool => ($task['status'] ?? '') === 'done'));

ob_start();
?>
<section class="page-title">
    <div>
        <h1>My Tasks</h1>
        <p><?= count($tasks) === 0 ? 'A simple space to organize what needs to get done.' : e($completedCount . ' of ' . count($tasks) . ' tasks completed') ?></p>
    </div>
    <a class="button button--primary" href="<?= e(base_url('pages/create/')) ?>"><span aria-hidden="true">+</span> Add Task</a>
</section>

<?php if ($tasks === []): ?>
    <section class="panel empty-state">
        <div class="empty-state__icon" aria-hidden="true">✓</div>
        <h2>No tasks yet</h2>
        <p>Add your first task above and start making progress.</p>
        <a class="button button--primary" href="<?= e(base_url('pages/create/')) ?>">Add your first task</a>
    </section>
<?php else: ?>
    <section class="task-list" aria-label="Task list">
        <?php foreach ($tasks as $task): ?>
            <?php $isDone = ($task['status'] ?? '') === 'done'; ?>
            <article class="task-card<?= $isDone ? ' task-card--done' : '' ?>">
                <form class="task-toggle" method="post" data-disable-on-submit>
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="id" value="<?= e($task['id']) ?>">
                    <button class="task-toggle__button" type="submit" data-loading-text="…" aria-label="<?= $isDone ? 'Mark ' . e($task['title']) . ' as not complete' : 'Mark ' . e($task['title']) . ' as complete' ?>">
                        <span aria-hidden="true"><?= $isDone ? '✓' : '' ?></span>
                    </button>
                </form>
                <div class="task-card__content">
                    <span class="status status--<?= e($task['status']) ?>">
                        <?= e(task_status_label($task['status'])) ?>
                    </span>
                    <h2><?= e($task['title']) ?></h2>
                    <?php if (($task['description'] ?? '') !== ''): ?>
                        <p><?= e($task['description']) ?></p>
                    <?php else: ?>
                        <p class="muted">No description added.</p>
                    <?php endif; ?>
                </div>
                <div class="task-card__actions">
                    <a class="button" href="<?= e(base_url('pages/view/?id=' . urlencode($task['id']))) ?>">Details</a>
                    <a class="button" href="<?= e(base_url('pages/edit/?id=' . urlencode($task['id']))) ?>">Edit</a>
                    <a class="button button--danger" href="<?= e(base_url('pages/delete/?id=' . urlencode($task['id']))) ?>" aria-label="Delete <?= e($task['title']) ?>">Delete</a>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
<?php

render_page('Tasks', ob_get_clean(), base_url('pages/tasks/style.css'));
