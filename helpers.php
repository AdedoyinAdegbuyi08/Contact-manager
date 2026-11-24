<?php
session_start();

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function pull_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals($_SESSION['csrf'] ?? '', $sent)) {
        http_response_code(400);
        exit('Invalid form token. Go back, refresh the page and try again.');
    }
}

function clean_input(array $source): array
{
    $field = fn(string $k): string => trim((string)($source[$k] ?? ''));
    return [
        'name'    => $field('name'),
        'email'   => $field('email'),
        'phone'   => $field('phone'),
        'company' => $field('company'),
        'notes'   => $field('notes'),
    ];
}

function validate_customer(array $data): array
{
    $errors = [];

    if ($data['name'] === '') {
        $errors['name'] = 'Name is required.';
    } elseif (mb_strlen($data['name']) > 100) {
        $errors['name'] = 'Name must be 100 characters or fewer.';
    }

    if ($data['email'] === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    } elseif (mb_strlen($data['email']) > 150) {
        $errors['email'] = 'Email must be 150 characters or fewer.';
    }

    if ($data['phone'] !== '' && !preg_match('/^\+?[0-9 ()-]{7,20}$/', $data['phone'])) {
        $errors['phone'] = 'Phone may contain digits, spaces, + ( ) - and be 7 to 20 characters.';
    }

    if (mb_strlen($data['company']) > 100) {
        $errors['company'] = 'Company must be 100 characters or fewer.';
    }

    if (mb_strlen($data['notes']) > 2000) {
        $errors['notes'] = 'Notes must be 2000 characters or fewer.';
    }

    return $errors;
}

function is_duplicate_error(PDOException $e): bool
{
    return $e->getCode() === '23000';
}

function get_id(): ?int
{
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false || $id === null ? null : $id;
}

function find_customer(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM customers WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}