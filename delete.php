<?php
require __DIR__ . '/db.php';
require __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    redirect('index.php');
}

verify_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if (!$id) {
    flash('error', 'Invalid customer.');
    redirect('index.php');
}

try {
    $stmt = db()->prepare('DELETE FROM customers WHERE id = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        flash('error', 'That customer no longer exists.');
    } else {
        flash('success', 'Customer deleted.');
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    flash('error', 'Could not delete the customer. Please try again.');
}

redirect('index.php');