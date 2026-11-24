<?php
require __DIR__ . '/db.php';
require __DIR__ . '/helpers.php';

$id = get_id();
$customer = $id ? find_customer($id) : null;

if (!$customer) {
    http_response_code(404);
    flash('error', 'That customer could not be found.');
    redirect('index.php');
}

$data   = $customer;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $data   = clean_input($_POST);
    $errors = validate_customer($data);

    if (!$errors) {
        try {
            $stmt = db()->prepare(
                'UPDATE customers
                    SET name = ?, email = ?, phone = ?, company = ?, notes = ?
                  WHERE id = ?'
            );
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['phone']   !== '' ? $data['phone']   : null,
                $data['company'] !== '' ? $data['company'] : null,
                $data['notes']   !== '' ? $data['notes']   : null,
                $id,
            ]);
            flash('success', 'Customer “' . $data['name'] . '” was updated.');
            redirect('view.php?id=' . $id);
        } catch (PDOException $e) {
            if (is_duplicate_error($e)) {
                $errors['email'] = 'Another customer already uses this email.';
            } else {
                error_log($e->getMessage());
                $errors['_form'] = 'Something went wrong while saving. Please try again.';
            }
        }
    }
}

$pageTitle   = 'Edit ' . $customer['name'];
$submitLabel = 'Save changes';
require __DIR__ . '/partials/header.php';
?>
<h1>Edit customer</h1>
<?php require __DIR__ . '/partials/form.php'; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>