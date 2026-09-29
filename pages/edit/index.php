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
$form = [
    'title' => $task['title'],
    'description' => $task['description'],
    'status' => $task['status'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$errors, $form] = validate_task($_POST);

    if ($errors === []) {
        $tasks = read_tasks();

        foreach ($tasks as &$item) {
            if (($item['id'] ?? '') === $id) {
                $item['title'] = $form['title'];
                $item['description'] = $form['description'];
                $item['status'] = $form['status'];
                $item['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        unset($item);

        save_tasks($tasks);
        set_flash('Task updated successfully.');
        redirect_to('pages/view/?id=' . urlencode($id));
    }
}

ob_start();
?>
<section class="page-title">
    <div>
        <h1>Edit Task</h1>
        <p>Update the task information and save your changes.</p>
    </div>
</section>

<section class="panel">
    <form class="form" method="post">
        <?php if ($errors !== []): ?>
            <ul class="error-list">
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="field">
            <label for="title">Title</label>
            <input id="title" name="title" type="text" value="<?= e($form['title']) ?>" required>
        </div>

        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description"><?= e($form['description']) ?></textarea>
        </div>

        <div class="field">
            <label for="status">Status</label>
            <select id="status" name="status">
                <?php foreach (task_statuses() as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $form['status'] === $value ? 'selected' : '' ?>>
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="actions">
            <button class="button button--primary" type="submit">Update Task</button>
            <a class="button button--quiet" href="<?= e(base_url('pages/view/?id=' . urlencode($id))) ?>">Cancel</a>
        </div>
    </form>
</section>
<?php

render_page('Edit Task', ob_get_clean(), base_url('pages/edit/style.css'));
