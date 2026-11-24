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

$pageTitle = $customer['name'];
require __DIR__ . '/partials/header.php';
?>
<h1><?= e($customer['name']) ?></h1>

<dl class="details">
  <dt>Email</dt>   <dd><a href="mailto:<?= e($customer['email']) ?>"><?= e($customer['email']) ?></a></dd>
  <dt>Phone</dt>   <dd><?= e($customer['phone']) ?: '—' ?></dd>
  <dt>Company</dt> <dd><?= e($customer['company']) ?: '—' ?></dd>
  <dt>Notes</dt>   <dd class="notes"><?= e($customer['notes']) ?: '—' ?></dd>
  <dt>Added</dt>   <dd><?= e($customer['created_at']) ?></dd>
  <dt>Updated</dt> <dd><?= e($customer['updated_at'] ?? $customer['created_at']) ?></dd>
</dl>

<div class="actions">
  <a class="btn" href="edit.php?id=<?= (int)$customer['id'] ?>">Edit</a>

  <form class="inline" method="post" action="delete.php"
        onsubmit="return confirm('Delete this customer? This cannot be undone.');">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int)$customer['id'] ?>">
    <button class="btn btn-danger" type="submit">Delete</button>
  </form>

  <a class="link" href="index.php">Back to list</a>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>