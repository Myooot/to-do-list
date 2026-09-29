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

    if (!csrf_token_is_valid($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Your session expired. Please try again.';
    }

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
    <form class="form" method="post">
        <?= csrf_field() ?>

        <?php if ($errors !== []): ?>
            <ul class="error-list">
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="field">
            <label for="title">Title</label>
            <input id="title" name="title" type="text" value="<?= e($form['title']) ?>" maxlength="<?= TASK_TITLE_MAX_LENGTH ?>" data-max-length="<?= TASK_TITLE_MAX_LENGTH ?>" aria-describedby="title-hint" required>
            <p class="field-hint" id="title-hint">0 / <?= TASK_TITLE_MAX_LENGTH ?> characters</p>
        </div>

        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" maxlength="<?= TASK_DESCRIPTION_MAX_LENGTH ?>" data-max-length="<?= TASK_DESCRIPTION_MAX_LENGTH ?>" aria-describedby="description-hint"><?= e($form['description']) ?></textarea>
            <p class="field-hint" id="description-hint">0 / <?= TASK_DESCRIPTION_MAX_LENGTH ?> characters</p>
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
            <button class="button button--primary" type="submit">Save Task</button>
            <a class="button button--quiet" href="<?= e(base_url('pages/tasks/')) ?>">Cancel</a>
        </div>
    </form>
</section>
<?php

render_page('Add Task', ob_get_clean(), base_url('pages/create/style.css'));
