<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/layout.php';

$errors = [];
$form = [
    'title' => '',
    'description' => '',
    'status' => 'pending',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$errors, $form] = validate_task($_POST);

    if ($errors === []) {
        $tasks = read_tasks();
        $tasks[] = [
            'id' => uniqid('task_', true),
            'title' => $form['title'],
            'description' => $form['description'],
            'status' => $form['status'],
            'created_at' => date('Y-m-d H:i:s'),
        ];

        save_tasks($tasks);
        set_flash('Task created successfully.');
        redirect_to('pages/tasks/');
    }
}

ob_start();
?>
<section class="page-title">
    <div>
        <h1>Add Task</h1>
        <p>Fill in the details below to create a new task.</p>
    </div>
</section>

<section class="panel">
    <form class="form" method="post" novalidate data-task-form data-disable-on-submit>
        <?php if ($errors !== []): ?>
            <ul class="error-list">
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="field">
            <label for="title">Title</label>
            <input id="title" name="title" type="text" value="<?= e($form['title']) ?>" maxlength="120" autocomplete="off" aria-describedby="title-hint title-error" aria-invalid="<?= $errors !== [] && $form['title'] === '' ? 'true' : 'false' ?>" required autofocus>
            <p class="field__hint" id="title-hint">Keep it short and specific.</p>
            <p class="field__error" id="title-error" <?= $errors !== [] && $form['title'] === '' ? '' : 'hidden' ?>>Please enter a task title.</p>
        </div>

        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" maxlength="1000" placeholder="Add any helpful details (optional)"><?= e($form['description']) ?></textarea>
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
            <button class="button button--primary" type="submit" data-loading-text="Adding task…">Add Task</button>
            <a class="button button--quiet" href="<?= e(base_url('pages/tasks/')) ?>">Cancel</a>
        </div>
    </form>
</section>
<?php

render_page('Add Task', ob_get_clean(), base_url('pages/create/style.css'));
