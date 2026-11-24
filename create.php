<?php
require __DIR__ . '/db.php';
require __DIR__ . '/helpers.php';

$data   = ['name' => '', 'email' => '', 'phone' => '', 'company' => '', 'notes' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $data   = clean_input($_POST);
    $errors = validate_customer($data);

    if (!$errors) {
        try {
            $stmt = db()->prepare(
                'INSERT INTO customers (name, email, phone, company, notes) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['phone']   !== '' ? $data['phone']   : null,
                $data['company'] !== '' ? $data['company'] : null,
                $data['notes']   !== '' ? $data['notes']   : null,
            ]);
            flash('success', 'Customer “' . $data['name'] . '” was added.');
            redirect('view.php?id=' . (int)db()->lastInsertId());
        } catch (PDOException $e) {
            if (is_duplicate_error($e)) {
                $errors['email'] = 'A customer with this email already exists.';
            } else {
                error_log($e->getMessage());
                $errors['_form'] = 'Something went wrong while saving. Please try again.';
            }
        }
    }
}

$pageTitle   = 'New customer';
$submitLabel = 'Add customer';
require __DIR__ . '/partials/header.php';
?>
<h1>New customer</h1>
<?php require __DIR__ . '/partials/form.php'; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>