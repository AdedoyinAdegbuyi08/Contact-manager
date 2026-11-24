<?php
$field = function (string $name, string $label, string $type = 'text', bool $required = false) use ($data, $errors): void {
    $hasError = isset($errors[$name]);
    ?>
    <div class="field<?= $hasError ? ' has-error' : '' ?>">
      <label for="<?= e($name) ?>"><?= e($label) ?><?= $required ? ' <span class="req">*</span>' : '' ?></label>
      <?php if ($type === 'textarea'): ?>
        <textarea id="<?= e($name) ?>" name="<?= e($name) ?>" rows="4"><?= e($data[$name] ?? '') ?></textarea>
      <?php else: ?>
        <input id="<?= e($name) ?>" type="<?= e($type) ?>" name="<?= e($name) ?>"
               value="<?= e($data[$name] ?? '') ?>"<?= $required ? ' required' : '' ?>>
      <?php endif; ?>
      <?php if ($hasError): ?><p class="error"><?= e($errors[$name]) ?></p><?php endif; ?>
    </div>
    <?php
};
?>
<form method="post" novalidate>
  <?= csrf_field() ?>
  <?php if (isset($errors['_form'])): ?>
    <div class="flash flash-error"><?= e($errors['_form']) ?></div>
  <?php endif; ?>
  <?php
  $field('name', 'Name', 'text', true);
  $field('email', 'Email', 'email', true);
  $field('phone', 'Phone');
  $field('company', 'Company');
  $field('notes', 'Notes', 'textarea');
  ?>
  <div class="actions">
    <button class="btn" type="submit"><?= e($submitLabel) ?></button>
    <a class="link" href="index.php">Cancel</a>
  </div>
</form>