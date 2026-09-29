<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/layout.php';

$tasks = read_tasks();

ob_start();
?>
<section class="page-title">
    <div>
        <h1>My Tasks</h1>
        <p>Keep track of simple tasks without a database.</p>
    </div>
    <a class="button button--primary" href="<?= e(base_url('pages/create/')) ?>">Add Task</a>
</section>

<?php if ($tasks === []): ?>
    <section class="panel empty-state">
        <h2>No tasks yet</h2>
        <p>Create your first task and it will appear here.</p>
        <a class="button button--primary" href="<?= e(base_url('pages/create/')) ?>">Create Task</a>
    </section>
<?php else: ?>
    <section class="task-list" aria-label="Task list">
        <?php foreach ($tasks as $task): ?>
            <article class="task-card">
                <div>
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
                    <a class="button" href="<?= e(base_url('pages/view/?id=' . urlencode($task['id']))) ?>">View</a>
                    <a class="button" href="<?= e(base_url('pages/edit/?id=' . urlencode($task['id']))) ?>">Edit</a>
                    <a class="button button--danger" href="<?= e(base_url('pages/delete/?id=' . urlencode($task['id']))) ?>">Delete</a>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
<?php

render_page('Tasks', ob_get_clean(), base_url('pages/tasks/style.css'));
