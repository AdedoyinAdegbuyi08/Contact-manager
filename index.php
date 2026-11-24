<?php
require __DIR__ . '/db.php';
require __DIR__ . '/helpers.php';

$q = trim((string)($_GET['q'] ?? ''));

try {
    if ($q !== '') {
        $like = '%' . $q . '%';
        $stmt = db()->prepare(
            'SELECT id, name, email, phone, company
               FROM customers
              WHERE name LIKE ? OR email LIKE ? OR company LIKE ?
              ORDER BY name'
        );
        $stmt->execute([$like, $like, $like]);
    } else {
        $stmt = db()->query('SELECT id, name, email, phone, company FROM customers ORDER BY name');
    }
    $customers = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $customers = [];
    flash('error', 'Could not load customers. Please try again.');
}

$pageTitle = 'Customers';
require __DIR__ . '/partials/header.php';
?>
<h1>Customers</h1>

<form class="search" method="get" action="index.php">
  <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search name, email or company" aria-label="Search customers">
  <button class="btn" type="submit">Search</button>
  <?php if ($q !== ''): ?><a class="link" href="index.php">Clear</a><?php endif; ?>
</form>

<?php if (!$customers): ?>
  <p class="empty">
    <?= $q !== '' ? 'No customers match “' . e($q) . '”.' : 'No customers yet. Add your first one.' ?>
  </p>
<?php else: ?>
  <p class="count"><?= count($customers) ?> customer<?= count($customers) === 1 ? '' : 's' ?></p>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>Name</th><th>Email</th><th>Phone</th><th>Company</th><th></th></tr>
      </thead>
      <tbody>
      <?php foreach ($customers as $c): ?>
        <tr>
          <td><a href="view.php?id=<?= (int)$c['id'] ?>"><?= e($c['name']) ?></a></td>
          <td><?= e($c['email']) ?></td>
          <td><?= e($c['phone']) ?></td>
          <td><?= e($c['company']) ?></td>
          <td class="row-actions">
            <a class="link" href="edit.php?id=<?= (int)$c['id'] ?>">Edit</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>