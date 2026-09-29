<?php

declare(strict_types=1);

const DATA_FILE = __DIR__ . '/../data/tasks.json';

function base_url(string $path = ''): string
{
    $root = rtrim(dirname($_SERVER['SCRIPT_NAME'], 3), '/\\');

    if ($root === '/' || $root === '\\') {
        $root = '';
    }

    return $root . '/' . ltrim($path, '/');
}

function redirect_to(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function read_tasks(): array
{
    if (!file_exists(DATA_FILE)) {
        return [];
    }

    $json = file_get_contents(DATA_FILE);
    $tasks = json_decode($json ?: '[]', true);

    return is_array($tasks) ? $tasks : [];
}

function save_tasks(array $tasks): void
{
    $dir = dirname(DATA_FILE);

    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents(DATA_FILE, json_encode(array_values($tasks), JSON_PRETTY_PRINT), LOCK_EX);
}

function find_task(string $id): ?array
{
    foreach (read_tasks() as $task) {
        if (($task['id'] ?? '') === $id) {
            return $task;
        }
    }

    return null;
}

function task_status_label(string $status): string
{
    return match ($status) {
        'done' => 'Done',
        'in_progress' => 'In Progress',
        default => 'Pending',
    };
}

function task_statuses(): array
{
    return [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'done' => 'Done',
    ];
}

function validate_task(array $input): array
{
    $errors = [];
    $title = trim((string)($input['title'] ?? ''));
    $description = trim((string)($input['description'] ?? ''));
    $status = (string)($input['status'] ?? 'pending');

    if ($title === '') {
        $errors[] = 'Task title is required.';
    }

    if (!array_key_exists($status, task_statuses())) {
        $errors[] = 'Please choose a valid status.';
    }

    return [$errors, [
        'title' => $title,
        'description' => $description,
        'status' => $status,
    ]];
}

function flash_message(): ?string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return is_string($message) ? $message : null;
}

function set_flash(string $message): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $_SESSION['flash'] = $message;
}
