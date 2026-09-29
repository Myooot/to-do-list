<?php

declare(strict_types=1);

const DATA_FILE = __DIR__ . '/../data/tasks.json';
const TASK_TITLE_MAX_LENGTH = 100;
const TASK_DESCRIPTION_MAX_LENGTH = 500;

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

    file_put_contents(DATA_FILE, json_encode(array_values($tasks), JSON_PRETTY_PRINT));
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

    if (string_length($title) > TASK_TITLE_MAX_LENGTH) {
        $errors[] = 'Task title must be 100 characters or fewer.';
    }

    if (string_length($description) > TASK_DESCRIPTION_MAX_LENGTH) {
        $errors[] = 'Task description must be 500 characters or fewer.';
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

function string_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
}

function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_token_is_valid(?string $token): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $storedToken = $_SESSION['csrf_token'] ?? '';

    return is_string($token)
        && is_string($storedToken)
        && $storedToken !== ''
        && hash_equals($storedToken, $token);
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
