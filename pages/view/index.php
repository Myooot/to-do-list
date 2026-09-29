<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/layout.php';

$id = (string)($_GET['id'] ?? '');
$task = $id !== '' ? find_task($id) : null;

if ($task === null) {
    set_flash('Task not found.');
    redirect_to('pages/tasks/');
}

ob_start();
?>
<section class="page-title">
    <div>
        <h1><?= e($task['title']) ?></h1>
        <p>Created <?= e($task['created_at'] ?? 'on an unknown date') ?></p>
    </div>
    <a class="button" href="<?= e(base_url('pages/tasks/')) ?>">Back</a>
</section>

<section class="panel detail">
    <dl>
        <div>
            <dt>Status</dt>
            <dd><span class="detail__status detail__status--<?= e($task['status']) ?>"><?= e(task_status_label($task['status'])) ?></span></dd>
        </div>
        <div>
            <dt>Description</dt>
            <dd><?= e($task['description'] !== '' ? $task['description'] : 'No description added.') ?></dd>
        </div>
    </dl>
    <div class="actions">
        <a class="button button--primary" href="<?= e(base_url('pages/edit/?id=' . urlencode($task['id']))) ?>">Edit Task</a>
        <a class="button button--danger" href="<?= e(base_url('pages/delete/?id=' . urlencode($task['id']))) ?>">Delete</a>
    </div>
</section>
<?php

render_page('View Task', ob_get_clean(), base_url('pages/view/style.css'));
